<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Menu;
use App\Page;
use App\Navigation;
use App\Site;

// Валидация
use App\Http\Requests\MenuRequest;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Session;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $site_alias)
    {
      $user = $request->user();

      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
                  ->whereSite_alias($site_alias)->first();
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
                  ->whereSite_alias($site_alias)->first();                       
        };
      }  
      // Создаем масив где ключ массива является ID меню
      $navigation_id = [];
      $navigation_tree = [];
      foreach ($site->navigations->toArray() as $navigation) {
        $navigation_id[$navigation['id']] = $navigation;
        $navigation_tree[$navigation['id']] = $navigation;
        foreach ($navigation_id as $navigation) {
          //Создаем масив где ключ массива является ID меню
          $navigation_id[$navigation['id']]['menus'] = [];
          foreach ($navigation['menus'] as $menu) {
            // dd($menu);
            $navigation_id[$navigation['id']]['menus'][$menu['id']] = $menu;
          }
          //Функция построения дерева из массива от Tommy Lacroix
          $navigation_tree[$navigation['id']]['menus'] = [];
          foreach ($navigation_id[$navigation['id']]['menus'] as $menu => &$node) {   
            //Если нет вложений
            if (!$node['menu_parent_id']){
              $navigation_tree[$navigation['id']]['menus'][$menu] = &$node;
            } 
            else { 
            //Если есть потомки то перебераем массив
            $navigation_id[$navigation['id']]['menus'][$node['menu_parent_id']]['children'][$menu] = &$node;
            }
          };
        }
      }
      $navigations = $site->navigations->pluck('navigation_name', 'id');
      // $menus = [];
      // foreach ($site->navigations as $navigation) {
      //   $menus[$navigation->id] = $navigation->menus->where('page_id', null)->pluck('menu_name', 'id');
      // };
      // dd($navigation_tree);
      $pages_list = $site->pages->pluck('page_name', 'id');
      $page_info = pageInfo('menus');
      return view('menus', compact('site', 'navigation_tree', 'page_info', 'pages_list', 'site_alias', 'menus', 'navigations'));
    }

    // После записи переходим на созданный пункт меню 
    public function current_menu(Request $request, $site_alias, $section_id, $item_id)
    {
      $user = $request->user();
      $site = Site::whereSite_alias($site_alias)->first();

     if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $site = Site::with('pages', 'navigations', 'navigations.menus', 'navigations.menus.page')
                  ->findOrFail($site->id);
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $site = Site::with('pages', 'navigations', 'navigations.menus', 'navigations.menus.page')
                  ->findOrFail($site->id);                       
        };
      }  
      // Создаем масив где ключ массива является ID меню
      $navigation_id = [];
      $navigation_tree = [];
      foreach ($site->navigations->toArray() as $navigation) {
        $navigation_id[$navigation['id']] = $navigation;
        $navigation_tree[$navigation['id']] = $navigation;
        foreach ($navigation_id as $navigation) {
          //Создаем масив где ключ массива является ID меню
          $navigation_id[$navigation['id']]['menus'] = [];
          foreach ($navigation['menus'] as $menu) {
            // dd($menu);
            $navigation_id[$navigation['id']]['menus'][$menu['id']] = $menu;
          }
          //Функция построения дерева из массива от Tommy Lacroix
          $navigation_tree[$navigation['id']]['menus'] = [];
          foreach ($navigation_id[$navigation['id']]['menus'] as $menu => &$node) {   
            //Если нет вложений
            if (!$node['menu_parent_id']){
              $navigation_tree[$navigation['id']]['menus'][$menu] = &$node;
            } 
            else { 
            //Если есть потомки то перебераем массив
            $navigation_id[$navigation['id']]['menus'][$node['menu_parent_id']]['children'][$menu] = &$node;
            }
          };
        }
      }
      $navigations = $site->navigations->pluck('navigation_name', 'id');
      // $menus = [];
      // foreach ($site->navigations as $navigation) {
      //   $menus = $navigation->menus->where('page_id', null)->pluck('menu_name', 'id');
      // };
      $pages_list = $site->pages->pluck('page_name', 'id');
      $page_info = pageInfo('menus');
      $data = [
        'section_name' => 'navigations',
        'item_name' => 'menus',
        'section_id' => $section_id,
        'item_id' => $item_id,
      ];
      // dd($data);
      return view('menus', compact('site', 'navigation_tree', 'page_info', 'pages_list', 'data', 'site_alias', 'navigations')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuRequest $request, $site_alias)
    {
      // dd($request);
      $user = $request->user();
      // Пишем раздел меню
      $menu = new Menu;
      $menu->menu_name = $request->menu_name;
      if (isset($request->menu_icon)) {
        $menu->menu_icon = $request->menu_icon;
      };
      if (isset($request->menu_alias)) {
        $menu->menu_alias = $request->menu_alias;
      };
      $menu->navigation_id = $request->navigation_id;
      $menu->menu_parent_id = $request->menu_parent_id;
      $menu->page_id = $request->page_id;
      // dd($menu->page_id = $request->page_id);
      $menu->company_id = $user->company_id;
      $menu->author_id = $user->id;
      $menu->save();
      // dd($menu);
      if ($menu) {
        return Redirect('/sites/'.$site_alias.'/current_menu/'.$menu->navigation_id.'/'.$menu->id);
      } else {
        abort(403, 'Ошибка при записи раздела меню!');
      };
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $site_alias, $id)
    {
      // Отдаем данные по меню
      $menu = Menu::findOrFail($id);
      $navigation = Navigation::with('menus')->findOrFail($menu->navigation_id);
      $menus = $navigation->menus->pluck('menu_name', 'id');
      // dd($menus);
      $result = [
        'menu_name' => $menu->menu_name,
        'menu_icon' => $menu->menu_icon,
        'menu_alias' => $menu->menu_alias,
        'page_id' => $menu->page_id,
        'navigation_id' => $menu->navigation_id,
        'menu_parent_id' => $menu->menu_parent_id,
        'menus' => $menus,
      ];
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MenuRequest $request, $site_alias, $id)
    {
      $user = $request->user();
      $menu = Menu::with('navigation')->findOrFail($id);
      $site_id = $menu->navigation->site_id;
      $menu->menu_name = $request->menu_name;
      $menu->menu_alias = $request->menu_alias;
      $menu->navigation_id = $request->navigation_id;
      $menu->menu_icon = $request->menu_icon;
      $menu->menu_parent_id = $request->menu_parent_id;
      $menu->page_id = $request->page_id;
      $menu->editor_id = $user->id;
      $menu->save();
      // dd($menu);
      if ($menu) {
        return Redirect('/sites/'.$site_alias.'/current_menu/'.$menu->navigation_id.'/'.$menu->id);
      } else {
        abort(403, 'Ошибка обновления раздела меню');
      };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $site_alias, $id)
    {
      $user = $request->user();
      $menu = Menu::with('navigation')->findOrFail($id);
      $navigation_id = $menu->navigation_id;
      $site_id = $menu->navigation->site_id;
      if (isset($menu->menu_parent_id)) {
        $parent_id = $menu->menu_parent_id;
      } else {
        $parent_id = 0;
      }
      if ($menu) {
        $menu->editor_id = $user->id;
        $menu->save();
        $menu = Menu::destroy($id);
       // Удаляем с обновлением
        if ($menu) {
          return Redirect('/sites/'.$site_alias.'/current_menu/'.$navigation_id.'/'.$parent_id);
        } else {
          abort(403, 'Ошибка при удалении меню');
        };
      } else {
        abort(403, 'Меню не найдено');
      };
    }
}
