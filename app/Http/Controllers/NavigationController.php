<?php

namespace App\Http\Controllers;

// Модели
use App\Navigation;
use App\Menu;
use App\Page;
use App\Site;
use App\NavigationsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\NavigationRequest;

class NavigationController extends Controller
{

    // Настройки контроллера
    public function __construct(Navigation $navigation)
    {
        $this->middleware('auth');
        $this->navigation = $navigation;
        $this->class = Navigation::class;
        $this->model = 'App\Navigation';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence,  getmethod(__FUNCTION__));

        $navigations = Navigation::with(['menus' => function ($query) {
            $query->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }])
        ->withCount('menus')
        ->whereHas('site', function ($q) use ($alias) {
            $q->whereAlias($alias);
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // После записи переходим на созданный пункт меню
        if($request->ajax()) {
            return view('navigations.navigations-list', ['navigations' => $navigations, 'item' => $request->item, 'id' => $request->id, 'class' => 'App\Navigation', 'entity' => $entity, 'type' => 'modal']);
        }

        return view('navigations.index',[
            'navigations' => $navigations,
            'page_info' => pageInfo($this->entity_alias),
            'parent_page_info' => pageInfo('sites'),
            'site' => Site::moderatorLimit(operator_right('sites', false, getmethod(__FUNCTION__)))
            ->whereAlias($alias)
            ->first(),
        ]);
    }


    public function create(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Navigation::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_sites = operator_right('sites', false, 'index');

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $site = Site::moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();

        $navigation = new Navigation;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_navigations_categories = operator_right('navigations_categories', false, 'index');

        // Главный запрос
        $navigations_categories = NavigationsCategory::moderatorLimit($answer_navigations_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $navigations_categories_list = get_select_tree($navigations_categories, null, null, null);
        // echo $navigations_categories_list;

        return view('navigations.create-first', ['navigation' => $navigation, 'site' => $site, 'navigations_categories_list' => $navigations_categories_list]);
    }


    public function store(NavigationRequest $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Navigation::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $navigation = new Navigation;

        // Системная запись
        $navigation->system_item = $request->system_item;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $navigation->moderation = 1;
        }

        // Делаем заглавной первую букву
        $navigation->name = get_first_letter($request->name);

        $navigation->navigations_category_id = $request->navigations_category_id;
        $navigation->display = $request->display;
        $navigation->site_id = $request->site_id;
        $navigation->company_id = $company_id;
        $navigation->author_id = $user_id;
        $navigation->save();

        if ($navigation) {

        // Переадресовываем на index
            return redirect()->action('NavigationController@index', ['id' => $navigation->id, 'alias' => $alias, 'item' => 'navigation']);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи навигации!'
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_sites = operator_right('sites', $this->entity_dependence, 'index');

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $site = Site::moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_categories = operator_right('navigations_categories', false, 'index');

        // Категории
        $navigations_categories = NavigationsCategory::moderatorLimit($answer_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $navigations_categories_list = get_select_tree($navigations_categories, $navigation->navigations_category_id, null, null);
        // echo $navigations_categories_list;

        return view('navigations.edit-first', ['navigation' => $navigation, 'site' => $site, 'navigations_categories_list' => $navigations_categories_list]);
    }

    public function update(NavigationRequest $request, $alias, $id)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $navigation->moderation = 1;
        } else {
            $navigation->moderation = $request->moderation;
        }

        // Модерация и системная запись
        $navigation->system_item = $request->system_item;
        $navigation->moderation = $request->moderation;

        // Делаем заглавной первую букву
        $navigation->name = get_first_letter($request->name);;

        $navigation->navigations_category_id = $request->navigations_category_id;
        $navigation->display = $request->display;
        $navigation->site_id = $request->site_id;
        $navigation->editor_id = $user->id;
        $navigation->save();

        if ($navigation) {

            // Переадресовываем на index
            return redirect()->action('NavigationController@index', ['id' => $navigation->id, 'alias' => $alias, 'item' => 'navigation']);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи навигации!'
            ];
        }
    }

    public function destroy(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::withCount('menus')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        // Удаляем ajax
        if ($navigation->count_menus > 0) {

            // Если содержит, то даем сообщенеи об ошибке
            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Получаем авторизованного пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            // Если нет, мягко удаляем
            $navigation->editor_id = $user_id;
            $navigation->save();

            // Если нет, мягко удаляем
            $navigation = Navigation::destroy($id);

            if ($navigation) {

            // Переадресовываем на index
                return redirect()->action('NavigationController@index', ['alias' => $alias, 'item' => 'navigation']);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при удалении навигации!'
                ];
            }
        };
    }

    // Проверка наличия в базе
    public function ajax_check(Request $request, $alias)
    {

        // Проверка навигации по сайту в нашей базе данных
        $navigation = Navigation::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->whereName($request->name)
        ->first();

        // Если такая навигация есть
        if ($navigation) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0,
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->navigations as $item) {
            Navigation::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Navigation::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Navigation::where('id', $request->id)->update(['display' => $display]);

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
