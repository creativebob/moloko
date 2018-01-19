<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Menu;
use App\Page;
use App\Navigation;
use App\Site;

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
    public function index(Request $request)
    {
      $user = Auth::user();
      // Пишем сайт в сессию
      session(['current_site' => $request->site_id]);
      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
                  ->findOrFail($request->site_id);
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
                  ->findOrFail($request->site_id);                       
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
      $pages = $site->pages->pluck('page_name', 'id');
      $page_info = Page::wherePage_alias('/menus')->whereSite_id('1')->first();
      return view('menus', compact('site', 'navigation_tree', 'page_info', 'pages'));
    }

    // После записи переходим на созданный пункт меню 
    public function current_menu(Request $request, $navigat, $menu_id)
    {
      $user = Auth::user();
      $site_id  = session('current_site');
     if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
                  ->findOrFail($site_id);
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
                  ->findOrFail($site_id);                       
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
      $pages = $site->pages->pluck('page_name', 'id');
      $page_info = Page::wherePage_alias('/menus')->whereSite_id('1')->first();
      $data = [
        'navigation_id' => $navigat,
        'menu_id' => $menu_id,
      ];
      // dd($data);
      return view('menus', compact('site', 'navigation_tree', 'page_info', 'pages', 'data')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = Auth::user();
      // Пишем раздел меню
      if (isset($request->section) && $request->section == 1) {
          $menu = new Menu;
          $menu->menu_name = $request->menu_name;
          $menu->navigation_id = $request->navigation_id;
          if (isset($request->menu_icon)) {
            $menu->menu_icon = $request->menu_icon;
          };
          if (isset($request->menu_parent_id)) {
            $menu->menu_parent_id = $request->menu_parent_id;
          };
          if ($user->company_id == null) {

          } else {
            $menu->company_id = $user->company_id;
          };
          $menu->author_id = $user->id;
          $menu->save();
          if ($menu) {
            return Redirect('/current_menu/'.$menu->navigation_id.'/'.$menu->id);
          } else {
            abort(403, 'Ошибка при записи раздела меню!');
          };
        };
      // Пишем пункт меню
      if (isset($request->page) && $request->page == 1) {
        $menu = new Menu;
        $menu->page_id = $request->page_id;
        if (isset($request->menu_parent_id)) {
          $menu->menu_parent_id = $request->menu_parent_id;
        };
        $menu->navigation_id = $request->navigation_id;
        if ($user->company_id == null) {

        } else {
          $menu->company_id = $user->company_id;
        }
        $menu->author_id = $user->id;
        $menu->save();
        if ($menu) {
          return Redirect('/current_menu/'.$menu->navigation_id.'/'.$menu->id);
        } else {
          abort(403, 'Ошибка записи раздела меню');
        };
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
    public function edit($id)
    {
      // Отдаем данные по меню
      $menu = Menu::findOrFail($id);
      $result = [
        'menu_name' => $menu->menu_name,
        'menu_icon' => $menu->menu_icon,
        'navigation_id' => $menu->navigation_id,
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
    public function update(Request $request, $id)
    {
      $user = Auth::user();
      $menu = Menu::with('navigation')->findOrFail($id);
      $site_id = $menu->navigation->site_id;
      $menu->menu_name = $request->menu_name;
      $menu->navigation_id = $request->navigation_id;
      $menu->menu_icon = $request->menu_icon;
      if (isset($request->menu_parent_id)) {
        $menu->menu_parent_id = $request->menu_parent_id;
      };
      if ($user->company_id == null) {

      } else {
        $menu->company_id = $user->company_id;
      }
      $menu->editor_id = $user->id;
      $menu->save();
      if ($menu) {
        return Redirect('/current_menu/'.$menu->navigation_id.'/'.$menu->id);
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
    public function destroy($id)
    {
      $user = Auth::user();
      $menu = Menu::with('navigation')->findOrFail($id);
      $site_id = $menu->navigation->site_id;
      if ($menu) {
        $menu->editor_id = $user->id;
        $menu->save();
        $menu = Menu::destroy($id);
       // Удаляем с обновлением
        if ($menu) {
          return Redirect('/current_menu/'.$menu->navigation_id.'/0');
        } else {
          abort(403, 'Ошибка при удалении меню');
        };
      } else {
        abort(403, 'Меню не найдено');
      };
    }
}
