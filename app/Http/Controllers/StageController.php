<?php

namespace App\Http\Controllers;

// Модели
use App\Stage;
use App\Page;
use App\User;
use App\Role;
use App\Staffer;
use App\StageRole;
use App\Sector;
use App\Entity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\StageRequest;

// Политика
use App\Policies\StagePolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;

// На удаление
use Illuminate\Support\Facades\Auth;

class StageController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'stages';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Stage::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $stages = Stage::with('author', 'company')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

 
        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'author',               // Автор записи
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('stages.index', compact('stages', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Stage::class);

        $stage = new Stage;

        $entities = Entity::get();
        $entities_list = $entities->pluck('name', 'id');

        $fields_list = Schema::getColumnListing($entities->first()->alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('stages.create', compact('stage', 'page_info', 'entities_list', 'fields_list'));  
    }

    public function store(StageRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Stage::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Создаем новую должность
        $stage = new Stage;
        $stage->name = $request->name;
        $stage->description = $request->description;
        $stage->company_id = $company_id;
        $stage->author_id = $user_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $stage->moderation = true;
        }

        // Системная запись
        $stage->system = $request->has('system');
        $stage->display = $request->has('display');

        $stage->save();

        // Если должность записалась
        if ($stage) {

            return redirect('/admin/stages');
        } else {
            abort(403, 'Ошибка записи этапа');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $stage = Stage::with('rules.field.entity')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $stage);

        $entities = Entity::get();
        $entities_list = $entities->pluck('name', 'id');

        // dd($entities_list);

        $fields = Schema::getColumnListing($entities->first()->alias);
        $fields_list = [];
        foreach ($fields as $field) {
            $fields_list[$field] = $field;
        }

        // dd($fields_list);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('stages.edit', compact('stage', 'page_info', 'entities_list', 'fields_list'));
    }

    public function update(StageRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $stage = Stage::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $stage);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Перезаписываем данные
        $stage->name = $request->name;
        $stage->description = $request->description;

        // Модерация и системная запись
        $stage->system = $request->has('system');
        $stage->moderation = $request->has('moderation');
        $stage->display = $request->has('display');

        $stage->editor_id = $user_id;
        $stage->save();

        // Если записалось
        if ($stage) {

            return redirect('/admin/stages');
        } else {
            abort(403, 'Ошибка обновления этапа');
        };
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $stage = Stage::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $stage);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        if (isset($stage)) {

            $stage->editor_id = $user->id;
            $stage->save();

            // Удаляем должность с обновлением
            $stage = Stage::destroy($id);

            if ($stage) {
                return redirect('/admin/stages');
            } else {
                abort(403, 'Ошибка при удалении этапа');
            } 
        } else {

            abort(403, 'Этап не найден');
        }
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->stages as $item) {
            Stage::where('id', $item)->update(['sort' => $i]);
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

        $item = Stage::where('id', $request->id)->update(['system' => $system]);

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

        $item = Stage::where('id', $request->id)->update(['display' => $display]);

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

}
