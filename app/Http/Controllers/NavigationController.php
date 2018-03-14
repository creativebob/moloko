<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Navigation;
use App\Menu;
use App\Page;
use App\Site;

// Валидация
use App\Http\Requests\NavigationRequest;
// Политика
use App\Policies\NavigationPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigationController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'navigations';
  protected $entity_dependence = false;

    public function index(Request $request, $site_alias)
    {
      // Получаем метод
      $method = __FUNCTION__;
      // Подключение политики
      $this->authorize($method, Navigation::class);
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right('sites', $this->entity_dependence, $method);
      // -------------------------------------------------------------------------------------------
      // ГЛАВНЫЙ ЗАПРОС
      // -------------------------------------------------------------------------------------------
      $site = Site::with(['navigations' => function ($query) {
        $query->orderBy('sort', 'asc');
      }, 'navigations.menus' => function ($query) {
        $query->orderBy('sort', 'asc');
      }, 'navigations.menus.page'])
      ->moderatorLimit($answer)
      ->companiesLimit($answer)
      ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer)
      ->systemItem($answer) // Фильтр по системным записям
      ->whereSite_alias($site_alias)
      ->first();

      // dd($site);

      $user = $request->user(); 
      // Создаем масив где ключ массива является ID меню
      $navigation_id = [];
      $navigation_tree = [];
      $navigations = $site->navigations->keyBy('id');

      // dd($navigations->toArray());
      // kjk rtkdjfs

      foreach ($navigations->toArray() as $navigation) {

        // dd($navigation);

        $navigation_tree[$navigation['id']] = $navigation;
        // Проверяем права на редактирование и удаление
        $edit = 0;
        $delete = 0;
        if ($user->can('update', $navigation)) {
          $edit = 1;
        };
        if ($user->can('delete', $navigation)) {
          $delete = 1;
        };
        $navigation_right = $navigation;

        $navigation_id[$navigation_right['id']] = $navigation_right;
        $navigation_id[$navigation_right['id']]['edit'] = $edit;
        $navigation_id[$navigation_right['id']]['delete'] = $delete;

        $navigation_tree[$navigation_right['id']]  = $navigation_right;
        $navigation_tree[$navigation_right['id']]['edit'] = $edit;
        $navigation_tree[$navigation_right['id']]['delete'] = $delete;

        // Проверяем прапва на редактирование и удаление
        foreach ($navigation->menus as $menu) {
          $edit = 0;
          $delete = 0;
          if ($user->can('update', $menu)) {
            $edit = 1;
          };
          if ($user->can('delete', $menu)) {
            $delete = 1;
          };
          $menu_right = $menu->toArray();
          
          $navigation_id[$navigation->id]['menus'][$menu->id] = $menu_right;
          $navigation_id[$navigation->id]['menus'][$menu->id]['edit'] = $edit;
          $navigation_id[$navigation->id]['menus'][$menu->id]['delete'] = $delete;

          $navigation_tree[$navigation->id]['menus'][$menu->id] = $menu_right;
          $navigation_tree[$navigation->id]['menus'][$menu->id]['edit'] = $edit;
          $navigation_tree[$navigation->id]['menus'][$menu->id]['delete'] = $delete;
        };
       
        foreach ($navigation_id as $navigation) {
          // Создаем масив где ключ массива является ID меню
          $navigation_id[$navigation['id']]['menus'] = [];
          foreach ($navigation['menus'] as $menu) {
            // dd($menu);
            $navigation_id[$navigation['id']]['menus'][$menu['id']] = $menu;
          };

          // Функция построения дерева из массива от Tommy Lacroix
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
        };
      };

      // dd($navigation_tree);
      dd($navigation_id);
      // $menus = [];
      // foreach ($site->navigations as $navigation) {
      //   $menus[$navigation->id] = $navigation->menus->where('page_id', null)->pluck('menu_name', 'id');
      // };
      // dd($navigation_tree);
      $navigations = $site->navigations->pluck('navigation_name', 'id');
      $pages_list = $site->pages->pluck('page_name', 'id');


      // Инфо о странице
      $page_info = pageInfo($this->entity_name);

      return view('navigations.index', compact('site', 'navigation_tree', 'page_info', 'pages_list', 'site_alias', 'menus', 'navigations'));
    }

    // После записи переходим на созданный пункт меню 
    public function current_navigation(Request $request, $site_alias, $section_id, $item_id)
    {
      // Получаем метод
      $method = 'index';
      // Подключение политики
      $this->authorize($method, Navigation::class);
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
      // -------------------------------------------------------------------------------------------
      // ГЛАВНЫЙ ЗАПРОС
      // -------------------------------------------------------------------------------------------
      $site = Site::with(['pages', 'navigations', 'navigations.menus', 'navigations.menus.page'])
      ->moderatorLimit($answer)
      ->companiesLimit($answer)
      ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer)
      ->systemItem($answer) // Фильтр по системным записям
      ->whereSite_alias($site_alias)
      ->first();
     $user = $request->user(); 
      // Создаем масив где ключ массива является ID меню
      $navigation_id = [];
      $navigation_tree = [];
      foreach ($site->navigations as $navigation) {
        $navigation_id[$navigation['id']] = $navigation;
        $navigation_tree[$navigation['id']] = $navigation->toArray();
        // Проверяем прапва на редактирование и удаление
        $edit = 0;
        $delete = 0;
        if ($user->can('update', $navigation)) {
          $edit = 1;
        };
        if ($user->can('delete', $navigation)) {
          $delete = 1;
        };
        $navigation_right = $navigation->toArray();
        $navigation_id[$navigation_right['id']] = $navigation_right;
        $navigation_id[$navigation_right['id']]['edit'] = $edit;
        $navigation_id[$navigation_right['id']]['delete'] = $delete;

        $navigation_tree[$navigation_right['id']] = $navigation_right;
        $navigation_tree[$navigation_right['id']]['edit'] = $edit;
        $navigation_tree[$navigation_right['id']]['delete'] = $delete;

        // Проверяем прапва на редактирование и удаление
        foreach ($navigation->menus as $menu) {
          $edit = 0;
          $delete = 0;
          if ($user->can('update', $menu)) {
            $edit = 1;
          };
          if ($user->can('delete', $menu)) {
            $delete = 1;
          };
          $menu_right = $menu->toArray();
          
          $navigation_id[$navigation->id]['menus'][$menu->id] = $menu_right;
          $navigation_id[$navigation->id]['menus'][$menu->id]['edit'] = $edit;
          $navigation_id[$navigation->id]['menus'][$menu->id]['delete'] = $delete;

          $navigation_tree[$navigation->id]['menus'][$menu->id] = $menu_right;
          $navigation_tree[$navigation->id]['menus'][$menu->id]['edit'] = $edit;
          $navigation_tree[$navigation->id]['menus'][$menu->id]['delete'] = $delete;
        };
       
        foreach ($navigation_id as $navigation) {
          // Создаем масив где ключ массива является ID меню
          $navigation_id[$navigation['id']]['menus'] = [];
          foreach ($navigation['menus'] as $menu) {
            // dd($menu);
            $navigation_id[$navigation['id']]['menus'][$menu['id']] = $menu;
          };

          // Функция построения дерева из массива от Tommy Lacroix
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
        };
        
      };    // $menus = [];
      // foreach ($site->navigations as $navigation) {
      //   $menus = $navigation->menus->where('page_id', null)->pluck('menu_name', 'id');
      // };
      $navigations = $site->navigations->pluck('navigation_name', 'id');
      $pages_list = $site->pages->pluck('page_name', 'id');

      // Инфо о странице
      $page_info = pageInfo($this->entity_name);

      $data = [
        'section_name' => 'navigations',
        'item_name' => 'menus',
        'section_id' => $section_id,
        'item_id' => $item_id,
      ];
      // dd($data);
      return view('navigations.index', compact('site', 'navigation_tree', 'page_info', 'pages_list', 'data', 'site_alias', 'navigations')); 
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


    public function store(NavigationRequest $request, $site_alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Navigation::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;
        $user_status = $user->god;
        $company_id = $user->company_id;

        $navigation = new Navigation;
        $navigation->navigation_name = $request->navigation_name;
        $navigation->site_id = $request->site_id;
        $navigation->company_id = $company_id;
        $navigation->author_id = $user_id;
        $navigation->category_navigation_id = 2;
        $navigation->save();
        // Пишем сайт в сессию
        session(['current_site' => $request->site_id]);
        if ($navigation) {
          return Redirect('/sites/'.$site_alias.'/current_navigation/'.$navigation->id.'/0');
        } else {
          abort(403, 'Ошибка при записи навигации!');
        };
    }

    public function show($id)
    {
        //
    }

    public function edit($site_alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::with('menus')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);
        
        // Отдаем данные по навигации
        $result = [
          'navigation_name' => $navigation->navigation_name,
          'menus' => $navigation->menus->where('page_id', null)->pluck('menu_name', 'id'),
        ];
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function update(NavigationRequest $request, $site_alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);
        
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);
        $user = $request->user();
        $navigation->navigation_name = $request->navigation_name;
        $navigation->site_id = $request->site_id;
        $navigation->company_id = $user->company_id;
        $navigation->editor_id = $user->id;
        $navigation->save();

        // Пишем сайт в сессию
        session(['current_site' => $request->site_id]);

        if ($navigation) {
          return Redirect('/sites/'.$site_alias.'/current_navigation/'.$navigation->id.'/0');
        } else {
          abort(403, 'Ошибка при записи навигации!');
        };
    }


    public function destroy(Request $request, $site_alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        $site_id = $navigation->site_id;
        $user = $request->user();

      if ($navigation) {
        $navigation->editor_id = $user->id;
        $navigation->save();
        // Удаляем навигацию с обновлением
        $navigation = Navigation::destroy($id);
        if ($navigation) {
          return Redirect('/sites/'.$site_alias.'navigations');
        } else {
          abort(403, 'Ошибка при удалении навигации');
        };
      } else {
        abort(403, 'Навигация не найдена');
      };
    }

  public function navigations_sort(Request $request)
  {
    $result = '';
    $i = 1;
    foreach ($request->navigations as $item) {

      $navigation = Navigation::findOrFail($item);
      $navigation->sort = $i;
      $navigation->save();

      $i++;
    }
  }
}
