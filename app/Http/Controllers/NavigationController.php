<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Navigation;
use App\Menu;
use App\Page;
use App\Site;
use App\NavigationsCategory;

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

  public function index(Request $request, $alias)
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
    }, 'navigations.menus.page', 'navigations.navigations_category'])
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereAlias($alias)
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
        if ($user->can('update', $navigation->menus->where('id', $id)->first())) {
          $edit = 1;
        }
        if ($user->can('delete', $navigation->menus->where('id', $id)->first())) {
          $delete = 1;
        }
        $navigation_id[$navigation->id]['menus'][$id]['edit'] = $edit;
        $navigation_id[$navigation->id]['menus'][$id]['delete'] = $delete;

        // dd($navigation->menus->where('id', $id));

        // Функция построения дерева из массива от Tommy Lacroix
        // Если нет вложений
        if (!$menu['parent_id']){
          $navigations_tree[$navigation->id]['menus'][$id] = &$menu;
        } else { 
        // Если есть потомки то перебераем массив
          $navigation_id[$navigation->id]['menus'][$menu['parent_id']]['children'][$id] = &$menu;
        }
      }
      
      // Записываем даныне навигации
      $navigations_tree[$navigation->id]['id'] = $navigation->id;
      $navigations_tree[$navigation->id]['name'] = $navigation->name;
      $navigations_tree[$navigation->id]['system_item'] = $navigation->system_item;
      $navigations_tree[$navigation->id]['display'] = $navigation->display;
      $navigations_tree[$navigation->id]['moderation'] = $navigation->moderation;
      $navigations_tree[$navigation->id]['navigations_category'] = $navigation->navigations_category;

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
    // dd($navigations_tree);
    $navigations = $site->navigations->pluck('name', 'id');
    $pages_list = $site->pages->pluck('name', 'id');

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('sites');

    // После записи переходим на созданный пункт меню 
    // if($request->ajax()){
    //   return view('navigations.navigations-list', ['navigations_tree' => $navigations_tree, 'item' => $request->item, 'id' => $request->id]); 
    // }

    // dd($navigations_tree);

    return view('navigations.index', compact('site', 'navigations_tree', 'page_info' , 'parent_page_info', 'pages_list', 'alias', 'menus', 'navigations'));
  }

  // После записи переходим на созданный пункт меню 
  public function get_content(Request $request, $alias)
  {
    // Подключение политики
    $this->authorize('index', Navigation::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('sites', $this->entity_dependence,  getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $site = Site::with(['navigations' => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'navigations.menus' => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'navigations.menus.page', 'navigations.navigations_category'])
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereAlias($alias)
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
        if ($user->can('update', $navigation->menus->where('id', $id)->first())) {
          $edit = 1;
        }
        if ($user->can('delete', $navigation->menus->where('id', $id)->first())) {
          $delete = 1;
        }
        $navigation_id[$navigation->id]['menus'][$id]['edit'] = $edit;
        $navigation_id[$navigation->id]['menus'][$id]['delete'] = $delete;

        // Функция построения дерева из массива от Tommy Lacroix
        // Если нет вложений
        if (!$menu['parent_id']){
          $navigations_tree[$navigation->id]['menus'][$id] = &$menu;
        } else { 
        // Если есть потомки то перебераем массив
          $navigation_id[$navigation->id]['menus'][$menu['parent_id']]['children'][$id] = &$menu;
        }
      }

      // Записываем даныне навигации
      $navigations_tree[$navigation->id]['id'] = $navigation->id;
      $navigations_tree[$navigation->id]['name'] = $navigation->name;
      $navigations_tree[$navigation->id]['system_item'] = $navigation->system_item;
      $navigations_tree[$navigation->id]['display'] = $navigation->display;
      $navigations_tree[$navigation->id]['moderation'] = $navigation->moderation;
      $navigations_tree[$navigation->id]['navigations_category'] = $navigation->navigations_category;

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

  public function create(Request $request, $alias)
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
    ->whereAlias($alias)
    ->first();

    $navigation = new Navigation;

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_navigations_categories = operator_right('navigations_categories', false, 'index');

    // Главный запрос
    $navigations_categories = NavigationsCategory::moderatorLimit($answer_navigations_categories)
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

    // Формируем дерево вложенности
    $navigations_categories_cat = [];
    foreach ($navigations_categories as $id => &$node) { 

      // Если нет вложений
      if (!$node['parent_id']) {

        $navigations_categories_cat[$id] = &$node;
      } else { 

        // Если есть потомки то перебераем массив
        $navigations_categories[$node['parent_id']]['children'][$id] = &$node;
      }
    }
    // dd($navigations_categories_cat);

    // Функция отрисовки option'ов
    function tplMenu($navigations_category, $padding) {

      if ($navigations_category['category_status'] == 1) {
        $menu = '<option value="'.$navigations_category['id'].'" class="first">'.$navigations_category['name'].'</option>';
      } else {
        $menu = '<option value="'.$navigations_category['id'].'">'.$padding.' '.$navigations_category['name'].'</option>';
      }

      // Добавляем пробелы вложенному элементу
      if (isset($navigations_category['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;

        $menu .= showCat($navigations_category['children'], $padding);
      }
      return $menu;
    }
        // Рекурсивно считываем наш шаблон
    function showCat($data, $padding){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding);
      }
      return $string;
    }

        // Получаем HTML разметку
    $navigations_categories_list = showCat($navigations_categories_cat, '');

    return view('navigations.create-first', ['navigation' => $navigation, 'site' => $site, 'navigations_categories_list' => $navigations_categories_list]); 
  }


  public function store(NavigationRequest $request, $alias)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Navigation::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Смотрим компанию пользователя
    $company_id = $user->company_id;
    if($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    }

    // Скрываем бога
    $user_id = hideGod($user);

    // Наполняем сущность данными
    $navigation = new Navigation;

    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if ($answer['automoderate'] == false){
      $navigation->moderation = 1;
    }

    // Системная запись
    $navigation->system_item = $request->system_item;

    // Если такая навигация не существует
    if ($request->first_item == 1) {
      $first = mb_substr($request->name, 0, 1, 'UTF-8'); //первая буква
      $last = mb_substr($request->name, 1); //все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $navigation_name = $first.$last;
      $navigation->name = $navigation_name;
    }

    $navigation->navigations_category_id = $request->navigations_category_id;
    $navigation->display = $request->display;
    $navigation->site_id = $request->site_id;
    $navigation->company_id = $company_id;
    $navigation->author_id = $user_id;
    $navigation->save();

    if ($navigation) {
      // Переадресовываем на index
      return redirect()->action('NavigationController@get_content', ['id' => $navigation->id, 'alias' => $alias, 'item' => 'navigation']);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи навигации!'
      ];
    }
  }

  public function show($id)
  {
    //
  }

  public function edit(Request $request, $alias, $id)
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
    ->whereAlias($alias)
    ->first();


    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_category = operator_right('navigations_categories', false, 'index');

        // Категории
    $navigations_categories = NavigationsCategory::moderatorLimit($answer_category)
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

        // Формируем дерево вложенности
    $navigations_categories_cat = [];
    foreach ($navigations_categories as $id => &$node) { 

          // Если нет вложений
      if (!$node['parent_id']) {
        $navigations_categories_cat[$id] = &$node;
      } else { 

          // Если есть потомки то перебераем массив
        $navigations_categories[$node['parent_id']]['children'][$id] = &$node;
      };

    };

        // dd($navigations_categories_cat);

        // Функция отрисовки option'ов
    function tplMenu($navigations_category, $padding, $id) {

      $selected = '';
      if ($navigations_category['id'] == $id) {
            // dd($id);
        $selected = ' selected';
      }

      if ($navigations_category['category_status'] == 1) {
        $menu = '<option value="'.$navigations_category['id'].'" class="first"'.$selected.'>'.$navigations_category['name'].'</option>';
      } else {
        $menu = '<option value="'.$navigations_category['id'].'"'.$selected.'>'.$padding.' '.$navigations_category['name'].'</option>';
      }

            // Добавляем пробелы вложенному элементу
      if (isset($navigations_category['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;

        $menu .= showCat($navigations_category['children'], $padding, $id);
      }
      return $menu;
    }
        // Рекурсивно считываем наш шаблон
    function showCat($data, $padding, $id){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding, $id);
      }
      return $string;
    }

        // Получаем HTML разметку
    $navigations_categories_list = showCat($navigations_categories_cat, '', $navigation->navigations_category_id);

    return view('navigations.edit-first', ['navigation' => $navigation, 'site' => $site, 'navigations_categories_list' => $navigations_categories_list]);
  }

  public function update(NavigationRequest $request, $alias, $id)
  {
    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Смотрим компанию пользователя
    $company_id = $user->company_id;
    if($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    }

    // Скрываем бога
    $user_id = hideGod($user);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);
    
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $navigation);

    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if ($answer['automoderate'] == false) {
      $navigation->moderation = 1;
    } else {
      $navigation->moderation = $request->moderation;
    }

    // Системная запись
    $navigation->system_item = $request->system_item;
    
    // Если такая навигация не существует
    if ($request->first_item == 1) {
      $first = mb_substr($request->name ,0, 1, 'UTF-8'); //первая буква
      $last = mb_substr($request->name, 1); //все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $navigation_name = $first.$last;
      $navigation->name = $navigation_name;
    }

    $navigation->navigations_category_id = $request->navigations_category_id;
    $navigation->display = $request->display;
    $navigation->site_id = $request->site_id;
    $navigation->editor_id = $user->id;
    $navigation->save();

    if ($navigation) {
      // Переадресовываем на index
      return redirect()->action('NavigationController@get_content', ['id' => $navigation->id, 'alias' => $alias, 'item' => 'navigation']);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи навигации!'
      ];
    }
  }

  public function destroy(Request $request, $alias, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $navigation = Navigation::withCount('menus')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $navigation);

    // Удаляем ajax
    

    if ($navigation->count_menus > 0) {
      // Если содержит, то даем сообщенеи об ошибке
      $result = [
        'error_status' => 1,
        'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
    } else {
      // Получаем авторизованного пользователя
      $user = $request->user();

      // Скрываем бога
      $user_id = hideGod($user);
      
      // Если нет, мягко удаляем
      $navigation->editor_id = $user_id;
      $navigation->save();

      // Если нет, мягко удаляем
      $navigation = Navigation::destroy($id);

      if ($navigation) {
        // Переадресовываем на index
        return redirect()->action('NavigationController@get_content', ['alias' => $alias, 'item' => 'navigation']);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при удалении навигации!'
        ];
      }
    };
  }

  // Проверка наличия в базе
  public function navigation_check(Request $request, $alias)
  {
    // Проверка навигации по сайту в нашей базе данных
    $name = $request->name;
    $site = Site::withCount(['navigations' => function($query) use ($name) {
      $query->whereName($name);
    }])->whereAlias($alias)->first();

    // Если такая навигация есть
    if ($site->navigations_count > 0) {
      $result = [
        'error_status' => 1,
      ];
    // Если нет
    } else {
      $result = [
        'error_status' => 0,
      ];
    }
    return json_encode($result, JSON_UNESCAPED_UNICODE);
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
