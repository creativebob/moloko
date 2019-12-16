<?php

namespace App\Http\Controllers;

// Модели
use App\Position;
use App\Page;
use App\User;
use App\Role;
use App\Staffer;
use App\PositionRole;
use App\Sector;
use App\Notification;
use App\Charge;
use App\Widget;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\PositionRequest;

// Политика
use App\Policies\PostionPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

// На удаление
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Position $position)
    {
        $this->middleware('auth');
        $this->position = $position;
        $this->class = Position::class;
        $this->model = 'App\Position';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $positions = Position::with([
            'author',
            'page',
            'roles',
            'company'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->booklistFilter($request)
        // ->filter($request, 'author_id')
        ->filter($request, 'company_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'company',                 // Компания
            // 'author',                  // Автор
            // 'city',                 // Город
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('positions.index', compact('positions', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        $position = Position::make();

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('positions.create', compact('position', 'page_info'));
    }

    public function store(PositionRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        if ($user->god == 1) {
            $user_id = 1;
        } else {
            $user_id = $user->id;
        };

        $company_id = $user->company_id;

        // Создаем новую должность
        $data = $request->input();
        $position = Position::create($data);

        // Если должность записалась
        if($position) {

            // Роли
            if (isset($request->roles)) {
                $position->roles()->sync($request->roles);
            }

            // Оповещения
            $position->notifications()->sync($request->notifications);

            // Обязанности
            $position->charges()->sync($request->charges);

            // Виджеты
            $position->widgets()->sync($request->widgets);


            return redirect()->route('positions.index');
        } else {
            abort(403, 'Ошибка записи должности');
        }
    }

    public function show($id)
    {
    //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $position = Position::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $position);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('positions.edit', compact('position', 'page_info'));
    }

    public function update(PositionRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        if ($user->god == 1) {
            $user_id = 1;
        } else {
            $user_id = $user->id;
        };

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $position = Position::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $position);

        // Выбираем существующие роли для должности на данный момент
        $position_roles = $position->roles;

        // Перезаписываем данные

        $data = $request->input();
        $result = $position->update($data);

        // Если записалось
        if ($result) {

            // Когда должность обновилась, обновляем пришедшие для нее роли
            // Роли
            if (isset($request->roles)) {
                $position->roles()->sync($request->roles);
            }
            // if (isset($request->roles)) {
            //     $position->roles()->sync($request->roles);
            // } else {
            //     // Если удалили последнюю роль для должности и пришел пустой массив
            //     $position->roles()->detach();
            // }

            // Смотрим оповещения
            if (isset($request->notifications)) {
                $notifications_sync = $position->notifications()->sync($request->notifications);
                // dd($notifications_sync);

                if ((count($notifications_sync['attached']) > 0) || (count($notifications_sync['detached']) > 0)) {
                    $users = User::whereHas('staff.position', function ($q) use ($position) {
                        $q->whereId($position->id);
                    })->get();

                    $notifications_message = "Изменения в оповещениях:\r\n\r\n";

                    if (count($notifications_sync['attached']) > 0) {
                        $notifications_message .= "Вам стали доступны оповещения:\r\n";
                        $notifications = Notification::findOrFail($notifications_sync['attached']);
                        foreach ($notifications as $notification) {
                            $notifications_message .= "   ".$notification->name."\r\n";
                        }
                    }

                    if (count($notifications_sync['detached']) > 0) {
                        $notifications_message .= "Вам больше недоступны оповещения:\r\n";
                        $notifications = Notification::findOrFail($notifications_sync['detached']);
                        foreach ($notifications as $notification) {
                            $notifications_message .= "   ".$notification->name."\r\n";
                        }

                        // $delete = $position->staff();
                        // Удаляем отключенные оповещения у пользователей
                        foreach ($users as $user) {
                            $user->notifications()->detach($notifications_sync['detached']);
                        }
                        // dd($users->whereNotNull('telegram_id'));
                    }
                    $notifications_message .= "\r\nОзнакомиться с изменениями можно на вкладке \"Мой профиль\"\r\n";

                    // dd($notifications_message);
                }

            } else {

                // Если удалили последнее оповещение для должности и пришел пустой массив
                $res = $position->notifications()->detach();
                if ($res > 0) {
                    $notifications_message = "Изменения в оповещениях:\r\n\r\n";
                    $notifications_message .= "Вы больше не имеете доступ ни к одному из оповещений.\r\n";

                    $users = User::whereHas('staff.position', function ($q) use ($position) {
                        $q->whereId($position->id);
                    })->get();
                        // $delete = $position->staff();
                        // Удаляем отключенные оповещения у пользователей
                    foreach ($users as $user) {
                        $user->notifications()->detach();
                    }

                }
                // dd($notifications_message);

            }
            if (isset($notifications_message)) {
                $destinations = $users->where('telegram_id', '!=', null);
                if (isset($destinations)) {
                    send_message($destinations, $notifications_message);
                }
            }

            // Обязанности
            $position->charges()->sync($request->charges);

            // Виджеты
            $position->widgets()->sync($request->widgets);

            // $users = User::whereHas('staff', function ($q) {
            //     $q->whereHas('position', function ($q) {
            //         $q->with('notifications')->where('id', $position->id);
            //     });
            // })->get();

            return redirect()->route('positions.index');
        } else {
            abort(403, 'Ошибка записи должности');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, true, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $position = Position::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $position);

        // Поулчаем авторизованного пользователя
        $user = $request->user();

        if (isset($position)) {

            $position->editor_id = $user->id;
            $position->save();

            // Удаляем должность с обновлением
            $position = Position::destroy($id);

            if ($position) {
                return redirect('/admin/positions');
            } else {
                abort(403, 'Ошибка при удалении должности');
            };
        } else {

            abort(403, 'Должность не найдена');
        }
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->positions as $item) {
            Position::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Position::where('id', $request->id)->update(['system' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = Position::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function positions_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_staff = operator_right('staff', 'true', 'index');

        // Смотрим на наличие должности в данном филиале, в массиве устанавливаем id должностей, которых не може тбыть более 1ой
        $direction = Staffer::where(['position_id' => 1, 'filial_id' => $request->filial_id])->moderatorLimit($answer_staff)->count();

        $repeat = [];

        if($direction == 1) {
            $repeat[] = 1;
        };

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $positions_list = Position::with('staff')->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->whereNotIn('id', $repeat)
        ->pluck('name', 'id');
        echo json_encode($positions_list, JSON_UNESCAPED_UNICODE);
    }
}
