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
      session(['current_navigation' => $request->navigation_id]);
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
      return view('menus', compact('menu_tree', 'navigation', 'data', 'page_info')); 
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

      // Пишем филиал
      if (isset($request->section_db)) {
        // По умолчанию значение 0
        // if ($request->filial_database == 0) {
        //   // Проверка города в нашей базе данных
        //   $city_name = $request->city_name;

        //   $cities = City::where('city_name', 'like', $city_name.'%')->get();
        //   $count = $cities->count();
        //   if ($count > 0) {
            
        //     $objRes = (object) [];
        //     foreach ($cities as $city) {
        //       $city_id = $city->id;
        //       $city_name = $city->city_name;

        //       if ($city->area_id == null) {
        //         $area_name = '';
        //         $region_name = $city->region->region_name;
        //       } else {
        //         $area_name = $city->area->area_name;
        //         $region_name = $city->area->region->region_name;
        //       };
          
        //       $objRes->city_id[] = $city_id;
        //       $objRes->city_name[] = $city_name;
        //       $objRes->area_name[] = $area_name;
        //       $objRes->region_name[] = $region_name;
        //     };

        //     $result = [
        //       'error_status' => 0,
        //       'cities' => $objRes,
        //       'count' => $count
        //     ];
        //   } else {
        //     $result = [
        //       'error_message' => 'Населенный пункт не существует в нашей базе данных, добавьте его!',
        //       'error_status' => 1
        //     ];
        //   };
        //   echo json_encode($result, JSON_UNESCAPED_UNICODE);
        // };
        // Если город найден, то меняем значение на 1, пишем в базу и отдаем результат
        if ($request->section_db == 1) {

          $section = new Menu;

          $section->menu_name = $request->section_name;
          if (isset($request->section_icon)) {
            $section->menu_icon = $request->section_icon;
          };
          $section->navigation_id = $request->navigation_id;
          if ($user->company_id == null) {

          } else {
            $section->company_id = $user->company_id;
          }
          $section->author_id = $user->id;

          $section->save();

          if ($section) {
            return Redirect('/current_menu/'.$section->id.'/0/0');
          } else {
            echo 'Ошибка записи раздела меню';
          };
          
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
      $user = Auth::user();

      // Пишем филиал
      if (isset($request->section_db)) {
        if ($request->section_db == 1) {

          $page = Page::findOrFail($id);

          $section->menu_name = $request->section_name;
          if (isset($request->section_icon)) {
            $section->menu_icon = $request->section_icon;
          };
          $section->navigation_id = $request->navigation_id;
          if ($user->company_id == null) {

          } else {
            $section->company_id = $user->company_id;
          }
          $section->author_id = $user->id;

          $section->save();

          if ($section) {
            return Redirect('/current_menu/'.$section->id.'/0/0');
          } else {
            echo 'Ошибка записи раздела меню';
          };
          
        };
      };
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $menu = Menu::findOrFail($id);

    if ($menu->menu_parent_id == null) {
      // Меняем раздел
      $result = [
        'section_name' => $menu->menu_name,
        'section_icon' => $menu->menu_icon,
        'navigation_id' => $menu->navigation_id,
      ];
    } 
    // else {
    //   // Меняем отдел

    //   if (isset($department->city_id)) {
    //     $city_id = $department->city_id; 
    //   } else {
    //     $city_id = '';
    //   };
    //   if (isset($department->city->city_name)) {
    //     $city_name = $department->city->city_name;
    //   } else {
    //     $city_name = '';
    //   };
    //   if (isset($department->department_address)) {
    //     $department_address = $department->department_address;
    //   } else {
    //     $department_address = '';
    //   };
    //   if (isset($department->department_phone)) {
    //     $department_phone = decorPhone($department->department_phone);
    //   } else {
    //     $department_phone = '';
    //   };
    //   $result = [
    //     'city_id' => $city_id,
    //     'city_name' => $city_name,
    //     'department_address' => $department_address,
    //     'department_phone' => $department_phone,
    //     'department_name' => $department->department_name,
    //     'department_parent_id' => $department->department_parent_id,
    //     'filial_id' => $department->filial_id,
    //   ];
    // };
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
      $menu = Menu::findOrFail($id);
      $navigation_id = $menu->navigation_id;
        // Удаляем с обновлением
        $menu = Menu::destroy($id);
        if ($menu) {
          return Redirect('/menus?navigation_id='.$navigation_id);
        } else {
          // В случае непредвиденной ошибки
          echo "Непредвиденная ошибка";
        };
    }
}
