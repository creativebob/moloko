<?php

namespace App\Http\Controllers;


use App\Menu;
use App\Page;
use App\Navigation;


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
      // Пишем сайт в сессию
      // session(['current_navigation' => $request->navigation_id]);

      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $menus = Menu::with('page')
                ->whereNavigation_id($request->navigation_id)
                ->systemItem($system_item) // Фильтр по системным записям
                ->get();
        $navigation = Navigation::findOrFail($request->navigation_id);
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $menus = Menu::with('page')->whereNavigation_id($request->navigation_id)->get();
          $navigation = Navigation::findOrFail($request->navigation_id);
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

      // dd($menu_tree);

      return view('menus', compact('menu_tree', 'page_info', 'navigation'));
      // dd($positions);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
