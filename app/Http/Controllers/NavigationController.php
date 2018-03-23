<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Navigation;
use App\Menu;
use App\Page;
use App\Site;

// Валидация
use App\Http\Requests\NavigationRequest;
use App\Http\Requests\MenuRequest;
// Политика
use App\Policies\NavigationPolicy;
use App\Policies\MenuPolicy;
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
    // Подключение политики
    $this->authorize( getmethod(__FUNCTION__), Navigation::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('sites', $this->entity_dependence,  getmethod(__FUNCTION__));

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
    $navigation_id = [];
    $navigations_tree = [];
    $navigations = $site->navigations->keyBy('id');

    foreach ($navigations as $navigation) {
      $navigation_id[$navigation->id]['menus'] = $navigation->menus->keyBy('id')->toArray();

      // Проверяем права на редактирование и удаление
      foreach ($navigation_id[$navigation->id]['menus'] as $id => &$menu) {

        $edit = 0;
        $delete = 0;
        if ($user->can('update', $navigation->menus->where('id', $id))) {
          $edit = 1;
        }
        if ($user->can('delete', $navigation->menus->where('id', $id))) {
          $delete = 1;
        }
        $navigation_id[$navigation->id]['menus'][$id]['edit'] = $edit;
        $navigation_id[$navigation->id]['menus'][$id]['delete'] = $delete;

        // Функция построения дерева из массива от Tommy Lacroix
        // Если нет вложений
        if (!$menu['menu_parent_id']){
          $navigations_tree[$navigation->id]['menus'][$id] = &$menu;
        } else { 
        // Если есть потомки то перебераем массив
        $navigation_id[$navigation->id]['menus'][$menu['menu_parent_id']]['children'][$id] = &$menu;
        }
      }
      
      // Записываем даныне навигации
      $navigations_tree[$navigation->id]['id'] = $navigation->id;
      $navigations_tree[$navigation->id]['navigation_name'] = $navigation->navigation_name;
      $navigations_tree[$navigation->id]['system_item'] = $navigation->system_item;
      $navigations_tree[$navigation->id]['moderation'] = $navigation->moderation;

      // Проверяем права на редактирование и удаление навигации
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $navigation)) {
        $edit = 1;
      }
      if ($user->can('delete', $navigation)) {
        $delete = 1;
      }
      $navigations_tree[$navigation->id]['edit'] = $edit;
      $navigations_tree[$navigation->id]['delete'] = $delete;
    }
    $navigations = $site->navigations->pluck('navigation_name', 'id');
    $pages_list = $site->pages->pluck('page_name', 'id');

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($navigations_tree);

    return view('navigations.index', compact('site', 'navigations_tree', 'page_info', 'pages_list', 'site_alias', 'menus', 'navigations'));
  }

  // После записи переходим на созданный пункт меню 
  public function get_content(Request $request, $site_alias)
  {
    // Подключение политики
    $this->authorize('index', Navigation::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

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
    $navigation_id = [];
    $navigations_tree = [];
    $navigations = $site->navigations->keyBy('id');

    foreach ($navigations as $navigation) {
      $navigation_id[$navigation->id]['menus'] = $navigation->menus->keyBy('id')->toArray();

      // Проверяем права на редактирование и удаление
      foreach ($navigation_id[$navigation->id]['menus'] as $id => &$menu) {

        $edit = 0;
        $delete = 0;
        if ($user->can('update', $navigation->menus->where('id', $id))) {
          $edit = 1;
        }
        if ($user->can('delete', $navigation->menus->where('id', $id))) {
          $delete = 1;
        }
        $navigation_id[$navigation->id]['menus'][$id]['edit'] = $edit;
        $navigation_id[$navigation->id]['menus'][$id]['delete'] = $delete;

        // Функция построения дерева из массива от Tommy Lacroix
        // Если нет вложений
        if (!$menu['menu_parent_id']){
          $navigations_tree[$navigation->id]['menus'][$id] = &$menu;
        } else { 
        // Если есть потомки то перебераем массив
        $navigation_id[$navigation->id]['menus'][$menu['menu_parent_id']]['children'][$id] = &$menu;
        }
      }
      
      // Записываем даныне навигации
      $navigations_tree[$navigation->id]['id'] = $navigation->id;
      $navigations_tree[$navigation->id]['navigation_name'] = $navigation->navigation_name;
      $navigations_tree[$navigation->id]['system_item'] = $navigation->system_item;
      $navigations_tree[$navigation->id]['moderation'] = $navigation->moderation;

      // Проверяем права на редактирование и удаление навигации
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $navigation)) {
        $edit = 1;
      }
      if ($user->can('delete', $navigation)) {
        $delete = 1;
      }
      $navigations_tree[$navigation->id]['edit'] = $edit;
      $navigations_tree[$navigation->id]['delete'] = $delete;
    }

    // dd($data);
    return view('navigations.navigations-list', ['navigations_tree' => $navigations_tree, 'item' => $request->item, 'id' => $request->id]); 
  }

  public function create(Request $request, $site_alias)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Navigation::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_site = operator_right('site', $this->entity_dependence, 'index');

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $site = Site::moderatorLimit($answer_site)
    ->companiesLimit($answer_site)
    ->filials($answer_site) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_site)
    ->systemItem($answer_site) // Фильтр по системным записям
    ->whereSite_alias($site_alias)
    ->first();

    $navigation = new Navigation;

    return view('navigations.create-navigation', ['navigation' => $navigation, 'site' => $site]); 
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
    // session(['current_site' => $request->site_id]);
    if ($navigation) {
      // Переадресовываем на index
      return redirect()->action('NavigationController@get_content', ['id' => $navigation->id, 'site_alias' => $site_alias, 'item' => 'navigation']);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи сектора!'
      ];
    }
  }

  public function show($id)
  {
      //
  }

  public function edit(Request $request, $site_alias, $id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $navigation);
    
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_site = operator_right('site', $this->entity_dependence, 'index');

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $site = Site::moderatorLimit($answer_site)
    ->companiesLimit($answer_site)
    ->filials($answer_site) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_site)
    ->systemItem($answer_site) // Фильтр по системным записям
    ->whereSite_alias($site_alias)
    ->first();

    return view('navigations.edit-navigation', ['navigation' => $navigation, 'site' => $site]);
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

    if ($navigation) {
      // Переадресовываем на index
      return redirect()->action('NavigationController@get_content', ['id' => $navigation->id, 'site_alias' => $site_alias, 'item' => 'navigation']);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи навигации!'
      ];
    }
  }

  public function destroy(Request $request, $site_alias, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $navigation);

    // Удаляем ajax
    // Проверяем содержит ли индустрия вложения
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_menu = operator_right('menus', false, getmethod(__FUNCTION__));

    $menu_parent = Menu::moderatorLimit($answer_menu)->whereNavigation_id($id)->first();

    // Получаем авторизованного пользователя
    $user = $request->user();

    if ($menu_parent) {
      // Если содержит, то даем сообщенеи об ошибке
      $result = [
        'error_status' => 1,
        'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
    } else {
      // Если нет, мягко удаляем
      $navigation->editor_id = $user->id;
      $navigation->save();

      // Если нет, мягко удаляем
      $navigation = Navigation::destroy($id);

      if ($navigation) {
        // Переадресовываем на index
        return redirect()->action('NavigationController@get_content', ['site_alias' => $site_alias, 'item' => 'navigation']);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи сектора!'
        ];
      }
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
