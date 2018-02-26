<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Menu;
use App\Page;
use App\Navigation;
use App\Site;
// Валидация
use App\Http\Requests\MenuRequest;
// Политика
use App\Policies\MenuPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Session;

class MenuController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'menus';
  protected $entity_dependence = false;

  public function index(Request $request, $site_alias)
  {
  //   // Получаем метод
  //   $method = __FUNCTION__;
  //   // Подключение политики
  //   $this->authorize($method, Menu::class);
  //   // Получаем из сессии необходимые данные (Функция находиться в Helpers)
  //   $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
  //   // -------------------------------------------------------------------------------------------
  //   // ГЛАВНЫЙ ЗАПРОС
  //   // -------------------------------------------------------------------------------------------
  //   $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
  //   ->moderatorLimit($answer)
  //   ->companiesLimit($answer)
  //   ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
  //   ->authors($answer)
  //   ->systemItem($answer) // Фильтр по системным записям
  //   ->whereSite_alias($site_alias)
  //   ->first();
  //   // dd($site);

  //   $user = $request->user(); 
  //   // Создаем масив где ключ массива является ID меню
  //   $navigation_id = [];
  //   $navigation_tree = [];
  //   foreach ($site->navigations as $navigation) {
  //     $navigation_id[$navigation['id']] = $navigation;
  //     $navigation_tree[$navigation['id']] = $navigation->toArray();
  //     // Проверяем прапва на редактирование и удаление
  //     $edit = 0;
  //     $delete = 0;
  //     if ($user->can('update', $navigation)) {
  //       $edit = 1;
  //     };
  //     if ($user->can('delete', $navigation)) {
  //       $delete = 1;
  //     };
  //     $navigation_right = $navigation->toArray();
  //     $navigation_id[$navigation_right['id']] = $navigation_right;
  //     $navigation_id[$navigation_right['id']]['edit'] = $edit;
  //     $navigation_id[$navigation_right['id']]['delete'] = $delete;

  //     $navigation_tree[$navigation_right['id']] = $navigation_right;
  //     $navigation_tree[$navigation_right['id']]['edit'] = $edit;
  //     $navigation_tree[$navigation_right['id']]['delete'] = $delete;

  //     // Проверяем прапва на редактирование и удаление
  //     foreach ($navigation->menus as $menu) {
  //       $edit = 0;
  //       $delete = 0;
  //       if ($user->can('update', $menu)) {
  //         $edit = 1;
  //       };
  //       if ($user->can('delete', $menu)) {
  //         $delete = 1;
  //       };
  //       $menu_right = $menu->toArray();
        
  //       $navigation_id[$navigation->id]['menus'][$menu->id] = $menu_right;
  //       $navigation_id[$navigation->id]['menus'][$menu->id]['edit'] = $edit;
  //       $navigation_id[$navigation->id]['menus'][$menu->id]['delete'] = $delete;

  //       $navigation_tree[$navigation->id]['menus'][$menu->id] = $menu_right;
  //       $navigation_tree[$navigation->id]['menus'][$menu->id]['edit'] = $edit;
  //       $navigation_tree[$navigation->id]['menus'][$menu->id]['delete'] = $delete;
  //     };
     
  //     foreach ($navigation_id as $navigation) {
  //       // Создаем масив где ключ массива является ID меню
  //       $navigation_id[$navigation['id']]['menus'] = [];
  //       foreach ($navigation['menus'] as $menu) {
  //         // dd($menu);
  //         $navigation_id[$navigation['id']]['menus'][$menu['id']] = $menu;
  //       };

  //       // Функция построения дерева из массива от Tommy Lacroix
  //       $navigation_tree[$navigation['id']]['menus'] = [];
  //       foreach ($navigation_id[$navigation['id']]['menus'] as $menu => &$node) {   
  //         //Если нет вложений
  //         if (!$node['menu_parent_id']){
  //           $navigation_tree[$navigation['id']]['menus'][$menu] = &$node;
  //         } 
  //         else { 
  //         //Если есть потомки то перебераем массив
  //         $navigation_id[$navigation['id']]['menus'][$node['menu_parent_id']]['children'][$menu] = &$node;
  //         }
  //       };
  //     };
      
  //   };
  // // dd($navigation_tree);
  //   // dd($navigation_id);
  //   // $menus = [];
  //   // foreach ($site->navigations as $navigation) {
  //   //   $menus[$navigation->id] = $navigation->menus->where('page_id', null)->pluck('menu_name', 'id');
  //   // };
  //   // dd($navigation_tree);
  //   $navigations = $site->navigations->pluck('navigation_name', 'id');
  //   $pages_list = $site->pages->pluck('page_name', 'id');
  //   $page_info = pageInfo($this->entity_name);
  //   return view('menus.index', compact('site', 'navigation_tree', 'page_info', 'pages_list', 'site_alias', 'menus', 'navigations'));
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
    // Получаем метод
    $method = 'create';
    // Подключение политики
    $this->authorize($method, Menu::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;
    // dd($request);
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
    $menu->company_id = $company_id;
    $menu->author_id = $user_id;
    $menu->save();
    // dd($menu);
    if ($menu) {
      return Redirect('/sites/'.$site_alias.'/current_navigation/'.$menu->navigation_id.'/'.$menu->id);
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
    // Получаем метод
    $method = 'update';
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::moderatorLimit($answer)->findOrFail($id);
    // Подключение политики
    $this->authorize($method, $menu);
   
    $navigation = Navigation::with('menus')->moderatorLimit($answer)->findOrFail($menu->navigation_id);
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

    // dd($request);
    // Получаем метод
    $method = __FUNCTION__;
    // Получаем авторизованного пользователя
    $user = $request->user();
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, $method);
    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);
    // Подключение политики
    $this->authorize($method, $menu);
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
      return Redirect('/sites/'.$site_alias.'/current_navigation/'.$menu->navigation_id.'/'.$menu->id);
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
    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $menu);
    $user = $request->user();
    $navigation_id = $menu->navigation_id;
    $site_id = $menu->navigation->site_id;
    if (isset($menu->menu_parent_id)) {
      $parent_id = $menu->menu_parent_id;
    } else {
      $parent_id = 0;
    };
    if ($menu) {
      $menu->editor_id = $user->id;
      $menu->save();
      $menu = Menu::destroy($id);
     // Удаляем с обновлением
      if ($menu) {
        return Redirect('/sites/'.$site_alias.'/current_navigation/'.$navigation_id.'/'.$parent_id);
      } else {
        abort(403, 'Ошибка при удалении меню');
      };
    } else {
      abort(403, 'Меню не найдено');
    };
  }
}
