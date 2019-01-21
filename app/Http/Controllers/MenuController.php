<?php

namespace App\Http\Controllers;

// Модели
use App\Menu;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\MenuRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

// Транслитерация
use Transliterate;


class MenuController extends Controller
{

    // Настройки контроллера
    public function __construct(Menu $menu)
    {
        $this->middleware('auth');
        $this->menu = $menu;
        $this->class = Menu::class;
        $this->model = 'App\Menu';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'modal';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request, $site_id, $navigation_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence,  getmethod(__FUNCTION__));

        $menus = Menu::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('navigation_id', $navigation_id)
        ->get();
        // dd($menus);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('includes.menu_views.category_list',
                [
                    'items' => $menus,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $menus->count(),
                    'id' => $request->id,
                ]
            );
        }

        // Отдаем на шаблон
        return view('menus.index', [
            'menus' => $menus,
            'page_info' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'navigation_id' => $navigation_id
        ]);
    }

    public function create(Request $request, $site_id, $navigation_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('menus.create', [
            'menu' => new $this->class,
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'site_id' => $site_id,
            'navigation_id' => $navigation_id
        ]);
    }

    public function store(MenuRequest $request, $site_id, $navigation_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $menu = new Menu;

        $menu->parent_id = $request->parent_id;
        $menu->navigation_id = $navigation_id;

        // Делаем заглавной первую букву
        $menu->name = get_first_letter($request->name);

        $menu->icon = $request->icon;
        $menu->alias = $request->alias;

        $menu->tag = empty($request->tag) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->tag;

        $menu->page_id = $request->page_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'store');

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $menu->moderation = 1;
        }

        // Системная запись
        $menu->system_item = $request->system_item;
        $menu->display = $request->display;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $menu->company_id = $user->company_id;
        $menu->author_id = hideGod($user);

        $menu->save();

        // Если к пункту меню привязана страница и мы выключаем/вкючаем его отображение на сайте, то и меняем отображение и у страницы
        if (isset($request->page_id)) {

            $menu->page()->save(['display' => $request->display]);

            // Находим страницу
            // $page = Page::where('id', $request->page_id)
            // ->first();

            // if ($request->display == 1) {
            //     $page->display = 1;
            // } else {
            //     $page->display = null;
            // }

            // $page->save();

            // // Если страница не обновилась
            // if ($page == false) {
            //     abort(403, 'Ошибка при изменении страницы связанной с пунктом меню');
            // }
        }

        if ($menu) {

            // Переадресовываем на index
            return redirect()->route('menus.index', ['site_id' => $site_id, 'navigation_id' => $navigation_id, 'id' => $menu->id]);

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

    public function edit(Request $request, $site_id, $navigation_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $menu = Menu::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $menu);

        return view('menus.edit', [
            'menu' => $menu,
            'parent_id' => $menu->parent_id,
            'category_id' => $menu->category_id,
            'site_id' => $site_id,
            'navigation_id' => $navigation_id
        ]);
    }

    public function update(MenuRequest $request, $site_id, $navigation_id, $id)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

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

    public function destroy(Request $request, $site_id, $navigation_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $menu = Menu::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $menu);

        // Скрываем бога
        $menu->editor_id = hideGod($request->user());
        $menu->save();

        $parent_id = $menu->parent_id;
        $navigation_id = $menu->navigation_id;

        $menu->delete();

        if ($menu) {

            // Переадресовываем на index
            return redirect()->route('menus.index', ['navigation_id' => $navigation_id, 'id' => $parent_id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }
    }

}
