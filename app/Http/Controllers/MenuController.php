<?php

namespace App\Http\Controllers;


use App\Menu;
use App\Page;
use App\Navigation;
use App\Site;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
      $others_item['user_id'] = $user->id;
      $system_item = null;

      $site = Site::findOrFail($request->site_id);


      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $navigations = Navigation::with(['menus', 'site', 'site.pages'])
                ->whereSite_id($request->site_id)
                ->systemItem($system_item) // Фильтр по системным записям
                ->get();
        $site = Site::with('pages')->findOrFail($request->site_id);
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $navigations = Navigation::with(['menus', 'menus.page'])
                                  ->whereSite_id($request->site_id)
                                  ->get();
          $site = Site::with('pages')->findOrFail($request->site_id);
        };
      }  
      // dd($navigations->toArray());
      // Создаем масив где ключ массива является ID меню
      $navigation_id = [];
      $navigation_tree = [];
      foreach ($navigations->toArray() as $navigation) {
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
      // $menu_tree = $navigations;
      // dd($navigation_tree[1]['menus']);
      $pages = $site->pages->pluck('page_name', 'id');
      // dd($site);

      $page_info = Page::wherePage_alias('/menus')->whereSite_id('1')->first();
      return view('menus', compact('site', 'navigation_tree', 'page_info', 'pages'));
      // dd($positions);
    }

    // После записи переходим на созданный пункт меню 
    public function current_menu(Request $request, $section, $cur_menu, $page)
    {
      $user = Auth::user();
      $others_item['user_id'] = $user->id;
      $system_item = null;
      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $menus = Menu::with('page')
                ->whereNavigation_id($request->session()->get('current_navigation'))
                ->systemItem($system_item) // Фильтр по системным записям
                ->get();
        $navigation = Navigation::findOrFail($request->session()->get('current_navigation'));
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $menus = Menu::with('page')->whereNavigation_id($request->session()->get('current_navigation'))->get();
          $navigation = Navigation::findOrFail($request->session()->get('current_navigation'));
        };
      }
      $menus = $menus->toArray();
      //Создаем масив где ключ массива является ID меню
      $menus_id = [];
      foreach ($menus as $menu) {
        $menus_id[$menu['id']] = $menu;
      };
      //Функция построения дерева из массива от Tommy Lacroix
      $menu_tree = [];
      foreach ($menus_id as $id => &$node) {   
        //Если нет вложений
        if (!$node['menu_parent_id']){
          $menu_tree[$id] = &$node;
        } else { 
        //Если есть потомки то перебераем массив
          $menus_id[$node['menu_parent_id']]['children'][$id] = &$node;
        }
      };
      $page_info = Page::wherePage_alias('/menus')->whereSite_id('1')->first();

      $data = [
        'section_id' => $section,
        'menu_id' => $cur_menu,
        'page_id' => $page,
      ];
      // dd($data);
      $pages = $navigation->site->pages->pluck('page_name', 'id');
      return view('menus', compact('menu_tree', 'navigation', 'data', 'page_info', 'pages')); 
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
          }
          $menu->author_id = $user->id;

          $menu->save();

          if ($menu) {
            return Redirect('menus?site_id='.$request->site_id);
          } else {
            echo 'Ошибка записи раздела меню';
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
            return Redirect('menus?site_id='.$request->site_id);
          } else {
            echo 'Ошибка записи раздела меню';
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
        return Redirect('/menus?site_id='.$site_id);
      } else {
        echo 'Ошибка записи раздела меню';
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
      $menu = Menu::with('navigation')->findOrFail($id);
      $site_id = $menu->navigation->site_id;
        // Удаляем с обновлением
        $menu = Menu::destroy($id);
        if ($menu) {
          return Redirect('/menus?site_id='.$site_id);
        } else {
          // В случае непредвиденной ошибки
          echo "Непредвиденная ошибка";
        };
    }
}
