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

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\PositionRequest;

// Политика
use App\Policies\PostionPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// На удаление
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'positions';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $positions = Position::with('author', 'page', 'roles', 'company')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'company_id')
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Position::with('author', 'company')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->get();

        // dd($filter_query);
        $filter['status'] = null;

        $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите компанию:', 'company', 'company_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ---------------------------------------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('positions.index', compact('positions', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        // Список посадочных страниц для должности
        $answer_pages = operator_right('pages', false, 'index');

        $pages_list = Page::moderatorLimit($answer_pages)
        ->whereSite_id(1) // Только для должностей посадочная страница системного сайта
        // ->companiesLimit($answer_pages)
        ->authors($answer_pages)
        ->systemItem($answer_pages) // Фильтр по системным записям
        ->template($answer_pages)
        ->pluck('name', 'id');

        // Список ролей для должности
        $answer_roles = operator_right('roles', false, 'index');
        $roles = Role::moderatorLimit($answer_roles)
        ->companiesLimit($answer_roles)
        ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_roles)
        ->systemItem($answer_roles) // Фильтр по системным записям
        ->template($answer_pages)
        ->get();

        $position = new Position;

        // Получаем список секторов
        $sectors = Sector::get()->keyBy('id')->toArray();
        $sectors_cat = [];
        foreach ($sectors as $id => &$node) {   
            //Если нет вложений
            if (!$node['parent_id']){
                $sectors_cat[$id] = &$node;
            } else { 
                //Если есть потомки то перебераем массив
                $sectors[$node['parent_id']]['children'][$id] = &$node;
            };
        };
        // dd($sectors_cat);
        $sectors_list = [];
        foreach ($sectors_cat as $id => &$node) {
            $sectors_list[$id] = &$node;
            if (isset($node['children'])) {
                foreach ($node['children'] as $id => &$node) {
                    $sectors_list[$id] = &$node;
                }
            };
        };

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('positions.create', compact('position', 'pages_list', 'roles', 'sectors_list', 'page_info'));  
    }

    public function store(PositionRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Position::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        if ($user->god == 1) {
            $user_id = 1;
        } else {
            $user_id = $user->id;
        };

        $company_id = $user->company_id;

        // Создаем новую должность
        $position = new Position;
        $position->company_id = $company_id;
        $position->name = $request->name;
        $position->page_id = $request->page_id;
        $position->author_id = $user_id;
        $position->sector_id = $user->company->sector_id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $position->moderation = 1;
        };

        // Пишем ID компании авторизованного пользователя
        if($company_id == null) {
            abort(403, 'Необходимо авторизоваться под компанией');
        };

        $position->save();

        // Если должность записалась
        if($position) {

            // Когда должность записалась, смотрим пришедшие для нее роли
            if (isset($request->roles)) {
                $roles = [];
                foreach ($request->roles as $role) {
                    $roles[$role] = [
                    'author_id' => $user_id,
                    ];
                }
                $position->roles()->attach($roles);
            }

            return Redirect('/positions');
        } else {
            abort(403, 'Ошибка записи должности');
        };
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
        $position = Position::with('roles')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $position);

        // Список посадочных страниц для должности
        $answer_pages = operator_right('pages', false, 'index');
        $pages_list = Page::moderatorLimit($answer_pages)
        // ->companiesLimit($answer_pages)
        ->whereSite_id(1) // Только для должностей посадочная страница системного сайта
        ->authors($answer_pages)
        ->systemItem($answer_pages) // Фильтр по системным записям
        ->template($answer_pages)
        ->pluck('name', 'id');

        // Список ролей для должности
        $answer_roles = operator_right('roles', false, 'index');
        $roles = Role::moderatorLimit($answer_roles)
        ->companiesLimit($answer_roles)
        ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_roles)
        ->systemItem($answer_roles) // Фильтр по системным записям
        ->template($answer_pages)
        ->get();

        // Получаем список секторов
        $sectors = Sector::get()->keyBy('id')->toArray();
        $sectors_cat = [];
        foreach ($sectors as $id => &$node) {  
            //Если нет вложений
            if (!$node['parent_id']){
                $sectors_cat[$id] = &$node;
            } else { 
                //Если есть потомки то перебераем массив
                $sectors[$node['parent_id']]['children'][$id] = &$node;
            };
        };

        // dd($sectors_cat);
        $sectors_list = [];
        foreach ($sectors_cat as $id => &$node) {
            $sectors_list[$id] = &$node;
            if (isset($node['children'])) {
                foreach ($node['children'] as $id => &$node) {
                    $sectors_list[$id] = &$node;
                }
            };
        };

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('positions.edit', compact('position', 'pages_list', 'roles', 'sectors_list', 'page_info'));
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
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $position = Position::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $position);

        // Выбираем существующие роли для должности на данный момент
        $position_roles = $position->roles;

        // Перезаписываем данные
        $position->name = $request->name;
        $position->page_id = $request->page_id;

        // $position->company_id = $user->company_id;
        $position->editor_id = $user_id;
        $position->save();

        // Если записалось
        if ($position) {

            // Когда должность обновилась, обновляем пришедшие для нее роли
            if (isset($request->roles)) {
                $roles = [];
                foreach ($request->roles as $role) {
                    $roles[$role] = [
                        'author_id' => $user_id,
                    ];
                }
                $position->roles()->sync($roles);
            } else {

                // Если удалили последнюю роль для должности и пришел пустой массив
                $delete = PositionRole::where('position_id', $id)->delete();
            }
            return Redirect('/positions');
        } else {
            abort(403, 'Ошибка записи должности');
        };
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

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
                return Redirect('/positions');
            } else {
                abort(403, 'Ошибка при удалении должности');
            }; 
        } else {

            abort(403, 'Должность не найдена');
        }
    }

    // Сортировка
    public function positions_sort(Request $request)
    {
        $result = '';
        $i = 1;
        foreach ($request->positions as $item) {

            $positions = Position::findOrFail($item);
            $positions->sort = $i;
            $positions->save();
            $i++;
        }
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
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
