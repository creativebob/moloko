<?php

namespace App\Http\Controllers;

use App\User;
use App\Staffer;
use App\Notification;
use App\Http\Requests\System\PositionRequest;
use App\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{

    /**
     * PositionController constructor
     * @param Position $position
     */
    public function __construct(Position $position)
    {
        $this->middleware('auth');
        $this->position = $position;
        $this->class = Position::class;
        $this->model = 'App\Position';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    /**
     * Отображение списка ресурсов
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
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
            'company',
            'actual_staff'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
//        ->template($answer)
        ->booklistFilter($request)
        // ->filter($request, 'author_id')
        ->where('archive', false)
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

        return view('system.pages.hr.positions.index', compact('positions', 'page_info', 'filter'));
    }

    /**
     * Показать форму для создания нового ресурса
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        $position = Position::make();

        $page_info = pageInfo($this->entity_alias);

        return view('system.pages.hr.positions.create', compact('position', 'page_info'));
    }

    /**
     * Сохранение только что созданного ресурса в хранилище
     *
     * @param PositionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(PositionRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        $data = $request->validated();
        $position = Position::create($data);

        if ($position) {

            // Роли
            $roles_access = session('access.all_rights.index-roles-allow');
            if ($roles_access) {
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
            abort(403, 'Ошибка записи');
        }
    }

    /**
     * Отображение указанного ресурса
     *
     * @param $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $position = Position::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $position);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('system.pages.hr.positions.edit', compact('position', 'page_info'));
    }

    /**
     * Обновление указанного ресурса в хранилище
     *
     * @param PositionRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(PositionRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $position = Position::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $position);

        $data = $request->validated();
        $result = $position->update($data);

        if ($result) {

            // Роли
            $roles_access = session('access.all_rights.index-roles-allow');
            if ($roles_access) {
                $position->roles()->sync($request->roles);
            }

            // Оповещения
            $position->notifications()->sync($request->notifications);

            // TODO - 26.03.20 - Блок с оповещениями, вынес в вопросы
            // Смотрим оповещения
//            if (isset($request->notifications)) {
//                $notifications_sync = $position->notifications()->sync($request->notifications);
//                // dd($notifications_sync);
//
//                if ((count($notifications_sync['attached']) > 0) || (count($notifications_sync['detached']) > 0)) {
//                    $users = User::whereHas('staff.position', function ($q) use ($position) {
//                        $q->whereId($position->id);
//                    })->get();
//
//                    $notifications_message = "Изменения в оповещениях:\r\n\r\n";
//
//                    if (count($notifications_sync['attached']) > 0) {
//                        $notifications_message .= "Вам стали доступны оповещения:\r\n";
//                        $notifications = Notification::findOrFail($notifications_sync['attached']);
//                        foreach ($notifications as $notification) {
//                            $notifications_message .= "   ".$notification->name."\r\n";
//                        }
//                    }
//
//                    if (count($notifications_sync['detached']) > 0) {
//                        $notifications_message .= "Вам больше недоступны оповещения:\r\n";
//                        $notifications = Notification::findOrFail($notifications_sync['detached']);
//                        foreach ($notifications as $notification) {
//                            $notifications_message .= "   ".$notification->name."\r\n";
//                        }
//
//                        // $delete = $position->staff();
//                        // Удаляем отключенные оповещения у пользователей
//                        foreach ($users as $user) {
//                            $user->notifications()->detach($notifications_sync['detached']);
//                        }
//                        // dd($users->whereNotNull('telegram_id'));
//                    }
//                    $notifications_message .= "\r\nОзнакомиться с изменениями можно на вкладке \"Мой профиль\"\r\n";
//
//                    // dd($notifications_message);
//                }
//
//            } else {
//
//                // Если удалили последнее оповещение для должности и пришел пустой массив
//                $res = $position->notifications()->detach();
//                if ($res > 0) {
//                    $notifications_message = "Изменения в оповещениях:\r\n\r\n";
//                    $notifications_message .= "Вы больше не имеете доступ ни к одному из оповещений.\r\n";
//
//                    $users = User::whereHas('staff.position', function ($q) use ($position) {
//                        $q->whereId($position->id);
//                    })->get();
//                        // $delete = $position->staff();
//                        // Удаляем отключенные оповещения у пользователей
//                    foreach ($users as $user) {
//                        $user->notifications()->detach();
//                    }
//
//                }
//                // dd($notifications_message);
//
//            }
//
//            if (isset($notifications_message)) {
//                $destinations = $users->where('telegram_id', '!=', null);
//                if (isset($destinations)) {
//                    send_message($destinations, $notifications_message);
//                }
//            }

            // Обязанности
            $position->charges()->sync($request->charges);

            // Виджеты
            $position->widgets()->sync($request->widgets);

            return redirect()->route('positions.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    /**
     * Удаление указанного ресурса из хранилища
     *
     * @param $id
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Архивация указанного ресурса
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        $position = Position::with([
            'actual_staff'
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        if ($position) {
            // Подключение политики
            $this->authorize('delete', $position);

            $position->archive = true;
            $position->editor_id = hideGod(auth()->user());
            $position->save();

            if ($position) {
                return redirect()->route('positions.index');
            } else {
                abort(403, 'Ошибка при архивации');
            }
        } else {
            abort(403, 'Не найдено');
        }
    }

    // Надо посмотреть используется ли
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
