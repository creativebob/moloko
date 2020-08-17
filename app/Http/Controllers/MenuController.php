<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\MenuUpdateRequest;
use App\Http\Requests\System\MenuStoreRequest;
use App\Menu;
use App\Navigation;
use App\Site;
use Illuminate\Http\Request;

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
        $this->entity_dependence = true;
        $this->type = 'modal';
    }

    use Photable;

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
            ->orderBy('sort')
        ->get();
        // dd($menus);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
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

        $site = Site::findOrFail($site_id);
        $navigation = Navigation::findOrFail($navigation_id);

        // Отдаем на шаблон
        return view('menus.index', [
            'menus' => $menus,
            'pageInfo' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'navigation_id' => $navigation_id,
            'site' => $site,
            'navigation' => $navigation
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

    public function store(MenuStoreRequest $request, $site_id, $navigation_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $data['navigation_id'] = $navigation_id;
        $menu = (new $this->class())->create($data);

        // Если к пункту меню привязана страница и мы выключаем/вкючаем его отображение на сайте, то и меняем отображение и у страницы
        // if (isset($menu->page_id)) {
        //     $menu->page()->save(['display' => $request->display]);
        // }

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

    public function update(MenuUpdateRequest $request, $site_id, $navigation_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $menu = Menu::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $menu);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $menu);
        $result = $menu->update($data);

        if ($result) {

            // Переадресовываем на index
            return redirect()->route('menus.index', ['site_id' => $site_id, 'navigation_id' => $navigation_id, 'id' => $menu->id]);
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
        $menu = Menu::with([
            'childs'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $menu);

        $parent_id = $menu->parent_id;

        $menu->delete();

        if ($menu) {

            // Переадресовываем на index
            return redirect()->route('menus.index', ['site_id' => $site_id, 'navigation_id' => $navigation_id, 'id' => $parent_id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }
    }

}
