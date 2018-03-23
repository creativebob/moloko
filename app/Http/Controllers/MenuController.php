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

  }

  public function create(Request $request, $site_alias)
  {   
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Menu::class);

    $item_parent = $request->menu_parent_id;
    $navigation_id = $request->navigation_id;

    // echo $navigation_id;

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('site', $this->entity_dependence, 'index');

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $site = Site::with(['navigations' => function ($query) use ($navigation_id) {
      $query->where('id', $navigation_id)->orderBy('sort', 'asc');
    }, 'navigations.menus' => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'navigations.menus.page'])
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    // ->whereSite_alias($site_alias)
    ->first();

    

    // dd($site);

    $user = $request->user();

    // Формируем дерево
    $navigation_id = [];
    $navigations_tree = [];
    $navigations = $site->navigations->keyBy('id');

    foreach ($navigations as $navigation) {
      $navigation_id[$navigation->id]['children'] = $navigation->menus->keyBy('id')->toArray();

      // Проверяем права на редактирование и удаление
      foreach ($navigation_id[$navigation->id]['children'] as $id => &$item) {

        // Функция построения дерева из массива от Tommy Lacroix
        // Если нет вложений
        if (!$item['menu_parent_id']){
          $navigations_tree[$navigation->id]['children'][$id] = &$item;
        } else { 
        // Если есть потомки то перебераем массив
        $navigation_id[$navigation->id]['children'][$item['menu_parent_id']]['children'][$id] = &$item;
        }
      }

      // Записываем даныне навигации
      $navigations_tree[$navigation->id]['id'] = $navigation->id;
      $navigations_tree[$navigation->id]['navigation_name'] = $navigation->navigation_name;
    }

    

    // Функция отрисовки option'ов
    function tplMenu($item, $padding, $parent) {

      $selected = '';
      if ($item['id'] == $parent) {
        $selected = ' selected';
      }

      if (isset($item['navigation_name'])) {
        $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['navigation_name'].'</option>';
      } else {
        $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['menu_name'].'</option>';
      }
      // Добавляем пробелы вложенному элементу
      if (isset($item['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;
        
        $menu .= showCat($item['children'], $padding, $parent);
      }
      return $menu;
      
    }
    // Рекурсивно считываем наш шаблон
    function showCat($data, $padding, $parent){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding, $parent);
      }
      return $string;
    }

    // Получаем HTML разметку
    $navigation_list = showCat($navigations_tree, '', $item_parent);


    // dd($navigation_list);

    $pages_list = '';
    foreach ($site->pages as $page) {
      $pages_list = $pages_list . '<option value="'.$page->id.'">'.$page->page_name.'</option>';
    }

    // echo $navigation_list;
    $menu = new Menu;

    return view('navigations.create-medium', ['menu' => $menu, 'navigation_list' => $navigation_list, 'pages_list' => $pages_list, 'site' => $site]); 

  }

  public function store(MenuRequest $request, $site_alias)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Menu::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $company_id = $user->company_id;

    // Пишем раздел меню
    $menu = new Menu;

    // Модерация и системная запись
    $menu->system_item = $request->system_item;
    $menu->moderation = $request->moderation;

    $menu->menu_name = $request->menu_name;
    $menu->menu_icon = $request->menu_icon;
    $menu->menu_alias = $request->menu_alias;

    // Если родителем является навигация
    if ($request->navigation_id == $request->menu_parent_id) {
      $menu->navigation_id = $request->navigation_id;
      $menu->menu_parent_id = null;
    } else {
      $menu->navigation_id = $request->navigation_id;
      $menu->menu_parent_id = $request->menu_parent_id;
    }

    $menu->page_id = $request->page_id;
    $menu->company_id = $company_id;
    $menu->author_id = $user_id;
    $menu->save();

    // dd($menu);
    if ($menu) {
      // Переадресовываем на index
      return redirect()->action('NavigationController@get_content', ['id' => $menu->id, 'site_alias' => $site_alias, 'item' => 'menu']);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи сектора!'
      ];
    }
  }


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
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::moderatorLimit($answer)->findOrFail($id);

    // echo $menu;
    
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $menu);

    $item_id = $id;
    if (isset($menu->menu_parent_id)) {
      $item_parent = $menu->menu_parent_id;
    } else {
      $item_parent = $menu->navigation_id;
    }
    $page_id = $menu->page_id;
    $navigation_id = $menu->navigation_id;

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_site = operator_right('site', $this->entity_dependence, 'index');

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $site = Site::with(['navigations' => function ($query) use ($navigation_id) {
      $query->where('id', $navigation_id)->orderBy('sort', 'asc');
    }, 'navigations.menus' => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'navigations.menus.page'])
    ->moderatorLimit($answer_site)
    ->companiesLimit($answer_site)
    ->filials($answer_site) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_site)
    ->systemItem($answer_site) // Фильтр по системным записям
    // ->whereSite_alias($site_alias)
    ->first();

    // dd($site);

    $user = $request->user();

    // Формируем дерево
    $navigation_id = [];
    $navigations_tree = [];
    $navigations = $site->navigations->keyBy('id');

    foreach ($navigations as $navigation) {
      $navigation_id[$navigation->id]['children'] = $navigation->menus->keyBy('id')->toArray();

      // Проверяем права на редактирование и удаление
      foreach ($navigation_id[$navigation->id]['children'] as $id => &$item) {

        // Функция построения дерева из массива от Tommy Lacroix
        // Если нет вложений
        if (!$item['menu_parent_id']){
          $navigations_tree[$navigation->id]['children'][$id] = &$item;
        } else { 
        // Если есть потомки то перебераем массив
        $navigation_id[$navigation->id]['children'][$item['menu_parent_id']]['children'][$id] = &$item;
        }
      }

      // Записываем даныне навигации
      $navigations_tree[$navigation->id]['id'] = $navigation->id;
      $navigations_tree[$navigation->id]['navigation_name'] = $navigation->navigation_name;
    }

    // dd($navigations_tree);

    // Функция отрисовки option'ов
    function tplMenu($item, $padding, $parent, $id) {

      // Убираем из списка пришедший пункт меню 
      if ($item['id'] != $id) {

        $selected = '';
        if ($item['id'] == $parent) {
          $selected = ' selected';
        }
        if (isset($item['navigation_name'])) {
          $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['navigation_name'].'</option>';
        } else {
          $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['menu_name'].'</option>';
        }
        // Добавляем пробелы вложенному элементу
        if (isset($item['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;
          
          $menu .= showCat($item['children'], $padding, $parent, $id);
        }
        return $menu;
      }
    }
    // Рекурсивно считываем наш шаблон
    function showCat($data, $padding, $parent, $id){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding, $parent, $id);
      }
      return $string;
    }

    // Получаем HTML разметку
    $navigation_list = showCat($navigations_tree, '', $item_parent, $item_id);

    // dd($navigation_list);

    $pages_list = '';
    foreach ($site->pages as $page) {
      $selected = '';
      if ($page_id == $page->id) {
        $selected = ' selected';
      }
      $pages_list = $pages_list . '<option value="'.$page->id.'"'.$selected.'>'.$page->page_name.'</option>';
    }

    // echo $pages_list;

    return view('navigations.edit-medium', ['menu' => $menu, 'navigation_list' => $navigation_list, 'pages_list' => $pages_list, 'site' => $site]); 
  }

  public function update(MenuRequest $request, $site_alias, $id)
  {

    // Получаем авторизованного пользователя
    $user = $request->user();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $menu);
    $site_id = $menu->navigation->site_id;
    $menu->menu_name = $request->menu_name;
    $menu->menu_alias = $request->menu_alias;
    $menu->menu_icon = $request->menu_icon;

    // Модерация и системная запись
    $menu->system_item = $request->system_item;
    $menu->moderation = $request->moderation;

    // Если родителем является навигация
    if ($request->navigation_id == $request->menu_parent_id) {
      $menu->navigation_id = $request->navigation_id;
      $menu->menu_parent_id = null;
    } else {
      $menu->navigation_id = $request->navigation_id;
      $menu->menu_parent_id = $request->menu_parent_id;
    }
    $menu->page_id = $request->page_id;
    $menu->editor_id = $user->id;
    $menu->save();

    // dd($menu);
    if ($menu) {
      // Переадресовываем на index
      return redirect()->action('NavigationController@get_content', ['id' => $menu->id, 'site_alias' => $site_alias, 'item' => 'menu']);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи сектора!'
      ];
    }
  }


  public function destroy(Request $request, $site_alias, $id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $menu);

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

  public function menus_sort(Request $request)
  {
    $result = '';
    $i = 1;
    foreach ($request->menus as $item) {

      $menu = Menu::findOrFail($item);
      $menu->sort = $i;
      $menu->save();

      $i++;
    }
  }
}
