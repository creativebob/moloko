<?php

namespace App\Http\Controllers;

// Модели
use App\Menu;
use App\Page;
use App\Navigation;
use App\Site;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\MenuRequest;

// Политика
use App\Policies\MenuPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Session;

class MenuController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'menus';
    protected $entity_dependence = false;

    public function index(Request $request, $alias)
    {
        //
    }

    public function create(Request $request, $alias)
    {   

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Menu::class);

        $item_parent = $request->parent_id;
        $navigation_id = $request->navigation_id;

        // echo $navigation_id;
        // $navigation_id = 17;

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
            ->orderBy('sort', 'asc');
        }, 'navigations.menus' => function ($query) use ($answer_menus) {
            $query->moderatorLimit($answer_menus)
            ->companiesLimit($answer_menus)
            ->authors($answer_menus)
            ->systemItem($answer_menus) // Фильтр по системным записям
            ->orderBy('sort', 'asc');
        }, 'navigations.menus.page' => function ($query) use ($answer_pages) {
            $query->moderatorLimit($answer_pages)
            ->companiesLimit($answer_pages)
            ->authors($answer_pages)
            ->systemItem($answer_pages) // Фильтр по системным записям
            ->orderBy('sort', 'asc');
        }, 'navigations.navigations_category' => function ($query) use ($answer_navigations_categories) {
            $query->moderatorLimit($answer_navigations_categories)
            ->companiesLimit($answer_navigations_categories)
            ->authors($answer_navigations_categories)
            ->systemItem($answer_navigations_categories) // Фильтр по системным записям
            ->template($answer_navigations_categories) // Выводим шаблоны в список
            ->orderBy('sort', 'asc');
        }])
        ->moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();

        // dd($site);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $user = $request->user();

        // Формируем дерево
        $navigation_id = [];
        $navigations_tree = [];
        $navigations = $site->navigations->keyBy('id');

        foreach ($navigations as $navigation) {
            $navigation_id[$navigation->id]['children'] = $navigation->menus->keyBy('id')->toArray();

            // Проверяем права на редактирование и удаление
            foreach ($navigation_id[$navigation->id]['children'] as $id => &$item) {

                // Функция построения дерева из массива от Tommy Lacroix
                // Если нет вложений
                if (!$item['parent_id']){
                    $navigations_tree[$navigation->id]['children'][$id] = &$item;
                } else { 

                    // Если есть потомки то перебераем массив
                    $navigation_id[$navigation->id]['children'][$item['parent_id']]['children'][$id] = &$item;
                }
            }

            // Записываем даныне навигации
            $navigations_tree[$navigation->id]['id'] = $navigation->id;
            $navigations_tree[$navigation->id]['name'] = $navigation->name;
        }

        // Функция отрисовки option'ов
        function tplMenu($item, $padding, $parent) {

            $selected = '';
            if ($item['id'] == $parent) {
                $selected = ' selected';
            }

            if (isset($item['navigation_id'])) {
                $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$item['name'].'</option>';
            } else {
                $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$padding.' '.$item['name'].'</option>';
            }

            // Добавляем пробелы вложенному элементу
            if (isset($item['children'])) {
                $i = 1;
                for($j = 0; $j < $i; $j++){
                    $padding .= '&nbsp;&nbsp;&nbsp;';
                }     
                $i++;

                $menu .= showCat($item['children'], $padding, $parent);
            }
            return $menu;
        }

        // Рекурсивно считываем наш шаблон
        function showCat($data, $padding, $parent){
            $string = '';
            $padding = $padding;
            foreach($data as $item){
                $string .= tplMenu($item, $padding, $parent);
            }

            return $string;
        }

        // Получаем HTML разметку
        $navigation_list = showCat($navigations_tree, '', $item_parent);

        // dd($navigation_list);

        $pages_list = '';
        foreach ($site->pages as $page) {
            $pages_list = $pages_list . '<option value="'.$page->id.'">'.$page->name.'</option>';
        }

        // echo $navigation_list;
        $menu = new Menu;

        return view('navigations.create-medium', compact('menu', 'navigation_list', 'pages_list', 'site')); 
        // return view('navigations.create-medium', ['menu' => $menu, 'navigation_list' => $navigation_list, 'pages_list' => $pages_list, 'site' => $site]);
    }

    public function store(MenuRequest $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Menu::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $menu = new Menu;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $navigation->moderation = 1;
        }

        // Системная запись
        $menu->system_item = $request->system_item;

        $menu->name = $request->name;
        $menu->icon = $request->icon;
        $menu->alias = $request->alias;

        // Если родителем является навигация
        if ($request->navigation_id == $request->parent_id) {
            $menu->navigation_id = $request->navigation_id;
            $menu->parent_id = null;
        } else {
            $menu->navigation_id = $request->navigation_id;
            $menu->parent_id = $request->parent_id;
        }

        $menu->display = $request->display;
        $menu->page_id = $request->page_id;

        // Если к пункту меню привязана страница и мы выключаем/вкючаем его отображение на сайте, то и меняем отображение и у страницы
        if (isset($request->page_id)) {

            // Находим страницу
            $page = Page::whereId($request->page_id)->first();

            if ($request->display == 1) {
                $page->display = 1;
            } else {
                $page->display = null;
            }

            $page->save();

            // Если страница не обновилась
            if ($page == false) {
                abort(403, 'Ошибка при изменении страницы связанной с пунктом меню');
            }
        }
        
        $menu->company_id = $user->company_id;
        $menu->author_id = $user_id;
        $menu->save();

        if ($menu) {
        // Переадресовываем на index
            return redirect()->action('NavigationController@index', ['id' => $menu->id, 'alias' => $alias, 'item' => 'menu']);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи пункта меню!'
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
        $menu = Menu::moderatorLimit($answer)->findOrFail($id);
        // echo $menu;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $menu);

        $item_id = $id;
        if (isset($menu->parent_id)) {
            $item_parent = $menu->parent_id;
        } else {
            $item_parent = $menu->navigation_id;
        }
        $page_id = $menu->page_id;
        $navigation_id = $menu->navigation_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_site = operator_right('site', $this->entity_dependence, 'index');

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

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
            ->orderBy('sort', 'asc');
        }, 'navigations.menus' => function ($query) use ($answer_menus) {
            $query->moderatorLimit($answer_menus)
            ->companiesLimit($answer_menus)
            ->authors($answer_menus)
            ->systemItem($answer_menus) // Фильтр по системным записям
            ->orderBy('sort', 'asc');
        }, 'navigations.menus.page' => function ($query) use ($answer_pages) {
            $query->moderatorLimit($answer_pages)
            ->companiesLimit($answer_pages)
            ->authors($answer_pages)
            ->systemItem($answer_pages) // Фильтр по системным записям
            ->orderBy('sort', 'asc');
        }, 'navigations.navigations_category' => function ($query) use ($answer_navigations_categories) {
            $query->moderatorLimit($answer_navigations_categories)
            ->companiesLimit($answer_navigations_categories)
            ->authors($answer_navigations_categories)
            ->systemItem($answer_navigations_categories) // Фильтр по системным записям
            ->template($answer_navigations_categories) // Выводим шаблоны в список
            ->orderBy('sort', 'asc');
        }])
        ->moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();

        // dd($site);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Формируем дерево
        $navigation_id = [];
        $navigations_tree = [];
        $navigations = $site->navigations->keyBy('id');

        foreach ($navigations as $navigation) {
            $navigation_id[$navigation->id]['children'] = $navigation->menus->keyBy('id')->toArray();

            // Проверяем права на редактирование и удаление
            foreach ($navigation_id[$navigation->id]['children'] as $id => &$item) {

                // Функция построения дерева из массива от Tommy Lacroix
                // Если нет вложений
                if (!$item['parent_id']){
                    $navigations_tree[$navigation->id]['children'][$id] = &$item;
                } else { 

                // Если есть потомки то перебераем массив
                    $navigation_id[$navigation->id]['children'][$item['parent_id']]['children'][$id] = &$item;
                }
            }

            // Записываем даныне навигации
            $navigations_tree[$navigation->id]['id'] = $navigation->id;
            $navigations_tree[$navigation->id]['name'] = $navigation->name;
        }
        // dd($navigations_tree);

        // Функция отрисовки option'ов
        function tplMenu($item, $padding, $parent, $id) {

            // Убираем из списка пришедший пункт меню 
            if ($item['id'] != $id) {

                $selected = '';
                if ($item['id'] == $parent) {
                    $selected = ' selected';
                }

                // dd($item);
                if (isset($item['navigation_id'])) {
                    $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$item['name'].'</option>';
                } else {
                    $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$padding.' '.$item['name'].'</option>';
                }

                // Добавляем пробелы вложенному элементу
                if (isset($item['children'])) {
                    $i = 1;
                    for($j = 0; $j < $i; $j++){
                        $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                    }     
                    $i++;

                    $menu .= showCat($item['children'], $padding, $parent, $id);
                }
                return $menu;
            }
        }

        // Рекурсивно считываем наш шаблон
        function showCat($data, $padding, $parent, $id){
            $string = '';
            $padding = $padding;
            foreach($data as $item){
                $string .= tplMenu($item, $padding, $parent, $id);
            }
            return $string;
        }

        // Получаем HTML разметку
        $navigation_list = showCat($navigations_tree, '', $item_parent, $item_id);
        // dd($navigation_list);

        $pages_list = '';
        foreach ($site->pages as $page) {
            $selected = '';
            if ($page_id == $page->id) {
                $selected = ' selected';
            }
            $pages_list = $pages_list . '<option value="'.$page->id.'"'.$selected.'>'.$page->name.'</option>';
        }

        // echo $pages_list;
        return view('navigations.edit-medium', compact('menu', 'navigation_list', 'pages_list', 'site')); 
    }

    public function update(MenuRequest $request, $alias, $id)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $menu);

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $menu->moderation = 1;
        } else {
            $menu->moderation = $request->moderation;
        }

        // Системная запись
        $menu->system_item = $request->system_item;

        $site_id = $menu->navigation->site_id;
        $menu->name = $request->name;
        $menu->alias = $request->alias;
        $menu->icon = $request->icon;

        // Если родителем является навигация
        if ($request->navigation_id == $request->parent_id) {
            $menu->navigation_id = $request->navigation_id;
            $menu->parent_id = null;
        } else {
            $menu->navigation_id = $request->navigation_id;
            $menu->parent_id = $request->parent_id;
        }

        $menu->display = $request->display;
        $menu->page_id = $request->page_id;

        // Если к пункту меню привязана страница и мы выключаем/вкючаем его отображение на сайте, то и меняем отображение и у страницы
        if (isset($request->page_id)) {

            // Находим страницу
            $page = Page::whereId($request->page_id)->first();

            if ($request->display == 1) {
                $page->display = 1;
            } else {
                $page->display = null;
            }

            $page->save();

            // Если страница не обновилась
            if ($page == false) {
                abort(403, 'Ошибка при изменении страницы связанной с пунктом меню');
            }
        }

        $menu->editor_id = $user_id;
        $menu->save();

        // dd($menu);
        if ($menu) {

            // Переадресовываем на index
            return redirect()->action('NavigationController@index', ['id' => $menu->id, 'alias' => $alias, 'item' => 'menu']);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлени пункта меню!'
            ];
        }
    }

    public function destroy(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $menu);


        $navigation_id = $menu->navigation_id;
        $site_id = $menu->navigation->site_id;
        if (isset($menu->parent_id)) {
            $parent_id = $menu->parent_id;
        } else {
            $parent_id = 0;
        }

        $navigation_id = $menu->navigation_id;

        if ($menu) {
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $menu->editor_id = $user_id;
            $menu->save();

            $menu = Menu::destroy($id);

            // Удаляем с обновлением
            if ($menu) {
            // Переадресовываем на index
                return redirect()->action('NavigationController@index', ['alias' => $alias, 'id' => $navigation_id, 'item' => 'navigation']);
            } else {
                abort(403, 'Ошибка при удалении меню');
            }
        } else {
            abort(403, 'Меню не найдено');
        }
    }

    public function menus_sort(Request $request)
    {
        $result = '';
        $i = 1;

        foreach ($request->menus as $item) {
            $menu = Menu::findOrFail($item);
            $menu->sort = $i;
            $menu->save();
            $i++;
        }
    }
}
