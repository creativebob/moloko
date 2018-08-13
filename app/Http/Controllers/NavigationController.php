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

// Политика
use App\Policies\NavigationPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class NavigationController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'navigations';
    protected $entity_dependence = false;

    public function index(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize( getmethod(__FUNCTION__), Navigation::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_navigations = operator_right($this->entity_name, $this->entity_dependence,  getmethod(__FUNCTION__));

        $answer_menus = operator_right('menus', false,  getmethod(__FUNCTION__));

        $answer_pages = operator_right('pages', false,  getmethod(__FUNCTION__));

        $answer_navigations_categories = operator_right('navigations_categories', false,  getmethod(__FUNCTION__));

        $answer_sites = operator_right('sites', false,  getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $site = Site::with(['navigations' => function ($query) use ($answer_navigations) {
            $query->moderatorLimit($answer_navigations)
            ->companiesLimit($answer_navigations)
            ->authors($answer_navigations)
            ->systemItem($answer_navigations) // Фильтр по системным записям
            ->withCount('menus')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }, 'navigations.menus' => function ($query) use ($answer_menus) {
            $query->moderatorLimit($answer_menus)
            ->companiesLimit($answer_menus)
            ->authors($answer_menus)
            ->systemItem($answer_menus) // Фильтр по системным записям
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }, 'navigations.menus.page' => function ($query) use ($answer_pages) {
            $query->moderatorLimit($answer_pages)
            ->companiesLimit($answer_pages)
            ->authors($answer_pages)
            ->systemItem($answer_pages) // Фильтр по системным записям
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }, 'navigations.navigations_category'
        //  => function ($query) use ($answer_navigations_categories) {
        //     $query->moderatorLimit($answer_navigations_categories)
        //     ->companiesLimit($answer_navigations_categories)
        //     ->authors($answer_navigations_categories)
        //     ->systemItem($answer_navigations_categories) // Фильтр по системным записям
        //     ->orderBy('sort', 'asc');
        // }
    ])
        ->moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();

        // $navigations = Navigation::with(['site', 'navigations.menus' => function ($query) {
        //   $query->orderBy('sort', 'asc');
        // }, 'navigations.menus.page', 'navigations.navigations_category'])
        // ->moderatorLimit($answer)
        // ->companiesLimit($answer)
        // ->authors($answer)
        // ->systemItem($answer) // Фильтр по системным записям
        // ->whereHas('site', function ($query) use ($alias) {
        //   $query->whereAlias($alias);
        // })
        // ->orderBy('sort', 'asc')
        // ->get();

        // dd($site->quantity);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $navigation_id = [];
        $navigations_tree = [];
        $navigations = $site->navigations->keyBy('id');

        foreach ($navigations as $navigation) {
            $navigation_id[$navigation->id]['menus'] = $navigation->menus->keyBy('id')->toArray();

            // Проверяем права на редактирование и удаление
            foreach ($navigation_id[$navigation->id]['menus'] as $id => &$menu) {

                $edit = 0;
                $delete = 0;

                if ($user->can('update', $navigation->menus->where('id', $id)->first())) {
                    $edit = 1;
                }

                if ($user->can('delete', $navigation->menus->where('id', $id)->first())) {
                    $delete = 1;
                }

                $navigation_id[$navigation->id]['menus'][$id]['edit'] = $edit;
                $navigation_id[$navigation->id]['menus'][$id]['delete'] = $delete;

                // dd($navigation->menus->where('id', $id));

                // Функция построения дерева из массива от Tommy Lacroix
                // Если нет вложений
                if (!$menu['parent_id']){
                    $navigations_tree[$navigation->id]['menus'][$id] = &$menu;
                } else { 

                // Если есть потомки то перебераем массив
                    $navigation_id[$navigation->id]['menus'][$menu['parent_id']]['children'][$id] = &$menu;
                }
            }

            // Записываем даныне навигации
            $navigations_tree[$navigation->id]['id'] = $navigation->id;
            $navigations_tree[$navigation->id]['name'] = $navigation->name;
            $navigations_tree[$navigation->id]['system_item'] = $navigation->system_item;
            $navigations_tree[$navigation->id]['display'] = $navigation->display;
            $navigations_tree[$navigation->id]['moderation'] = $navigation->moderation;
            $navigations_tree[$navigation->id]['navigations_category'] = $navigation->navigations_category;
            $navigations_tree[$navigation->id]['menus_count'] = $navigation->menus_count;

            // Проверяем права на редактирование и удаление навигации
            $edit = 0;
            $delete = 0;

            if ($user->can('update', $navigation)) {
                $edit = 1;
            }

            if ($user->can('delete', $navigation)) {
                $delete = 1;
            }

            $navigations_tree[$navigation->id]['edit'] = $edit;
            $navigations_tree[$navigation->id]['delete'] = $delete;
        }
        // dd($navigations_tree);

        $navigations = $site->navigations->pluck('name', 'id');
        $pages_list = $site->pages->pluck('name', 'id');

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        // После записи переходим на созданный пункт меню
        if($request->ajax()) {
            return view('navigations.navigations-list', ['navigations_tree' => $navigations_tree, 'item' => $request->item, 'id' => $request->id]); 
        }

        return view('navigations.index', compact('site', 'navigations_tree', 'page_info' , 'parent_page_info', 'pages_list', 'alias', 'menus', 'navigations'));
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
        ->get(['id','name','category_status','parent_id'])
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
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
        ->get(['id','name','category_status','parent_id'])
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
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
    public function navigation_check(Request $request, $alias)
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

    public function navigations_sort(Request $request)
    {

        $i = 1;

        foreach ($request->navigations as $item) {
            Navigation::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $navigation = Navigation::findOrFail($request->id);
        $navigation->display = $display;
        $navigation->save();

        if ($navigation) {

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
