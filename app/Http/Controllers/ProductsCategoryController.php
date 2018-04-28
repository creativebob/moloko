<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\ProductsCategory;

// Валидация
use App\Http\Requests\ProductsCategoryRequest;

// Политика
use App\Policies\ProductsCategoryPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsCategoryController extends Controller
{
      // Сущность над которой производит операции контроллер
  protected $entity_name = 'products_categories';
  protected $entity_dependence = false;

    public function index(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));


    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $products_categories = ProductsCategory::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $products_categories_rights = [];
    $products_categories_rights = $products_categories->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $products_categories_id = [];
    foreach ($products_categories_rights as $products_category) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $products_category)) {
        $edit = 1;
      };
      if ($user->can('delete', $products_category)) {
        $delete = 1;
      };
      $products_category_right = $products_category->toArray();
      $products_categories_id[$products_category_right['id']] = $products_category_right;
      $products_categories_id[$products_category_right['id']]['edit'] = $edit;
      $products_categories_id[$products_category_right['id']]['delete'] = $delete;
    };

    // dd($products_categories_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $products_categories_tree = [];
    foreach ($products_categories_id as $id => &$node) {   
      // Если нет вложений
      if (!$node['parent_id']){
        $products_categories_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $products_categories_id[$node['parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($products_categories_tree as $products_category) {
      $count = 0;
      if (isset($products_category['children'])) {
        $count = count($products_category['children']) + $count;
      };
      $products_categories_tree[$products_category['id']]['count'] = $count;
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($products_categories_tree);

    // Отдаем Ajax
    if($request->ajax()) {
      return view('products_categories.category-list', ['products_categories_tree' => $products_categories_tree, 'id' => $request->id]);
    }
    // Отдаем на шаблон
    return view('products_categories.index', compact('products_categories_tree', 'page_info'));
  }

  public function get_content(Request $request)
  {
    // Политика
    $this->authorize(getmethod('index'), ProductsCategory::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $products_categories = ProductsCategory::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $products_categories_rights = [];
    $products_categories_rights = $products_categories->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $products_categories_id = [];
    foreach ($products_categories_rights as $products_category) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $products_category)) {
        $edit = 1;
      };
      if ($user->can('delete', $products_category)) {
        $delete = 1;
      };
      $products_category_right = $products_category->toArray();
      $products_categories_id[$products_category_right['id']] = $products_category_right;
      $products_categories_id[$products_category_right['id']]['edit'] = $edit;
      $products_categories_id[$products_category_right['id']]['delete'] = $delete;
    };

    // dd($products_categories_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $products_categories_tree = [];
    foreach ($products_categories_id as $id => &$node) {   
      // Если нет вложений
      if (!$node['parent_id']){
        $products_categories_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $products_categories_id[$node['parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($products_categories_tree as $products_category) {
      $count = 0;
      if (isset($products_category['children'])) {
        $count = count($products_category['children']) + $count;
      };
      $products_categories_tree[$products_category['id']]['count'] = $count;
    };

    // Отдаем Ajax
    return view('products_categories.category-list', ['products_categories_tree' => $products_categories_tree, 'id' => $request->id]);
  }
    public function create(Request $request)
  {
      // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

    $products_category = new ProductsCategory;

    if (isset($request->parent_id)) {

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

      // Главный запрос
      $products_categories = ProductsCategory::moderatorLimit($answer)
      ->orderBy('sort', 'asc')
      ->get(['id','name','category_status','parent_id'])
      ->keyBy('id')
      ->toArray();

      // dd($products_categories);

      // Формируем дерево вложенности
      $products_categories_cat = [];
      foreach ($products_categories as $id => &$node) { 

        // Если нет вложений
        if (!$node['parent_id']) {
          $products_categories_cat[$id] = &$node;
        } else { 

        // Если есть потомки то перебераем массив
          $products_categories[$node['parent_id']]['children'][$id] = &$node;
        };
      };

      // dd($products_categories_cat);

      // Функция отрисовки option'ов
      function tplMenu($products_category, $padding, $parent) {

        $selected = '';
        if ($products_category['id'] == $parent) {
          $selected = ' selected';
        }
        if ($products_category['category_status'] == 1) {
          $menu = '<option value="'.$products_category['id'].'" class="first"'.$selected.'>'.$products_category['name'].'</option>';
        } else {
          $menu = '<option value="'.$products_category['id'].'"'.$selected.'>'.$padding.' '.$products_category['name'].'</option>';
        }
        
        // Добавляем пробелы вложенному элементу
        if (isset($products_category['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;
          
          $menu .= showCat($products_category['children'], $padding, $parent);
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
      $products_categories_list = showCat($products_categories_cat, '', $request->parent_id);

      // echo $products_categories_list;


      return view('products_categories.create-medium', ['products_category' => $products_category, 'products_categories_list' => $products_categories_list]);
    } else {
      return view('products_categories.create-first', ['products_category' => $products_category]);
    }
  }

    public function store(Request $request)
    {
       // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

      // Получаем данные для авторизованного пользователя
      $user = $request->user();
      $company_id = $user->company_id;
      if ($user->god == 1) {
      // Если бог, то ставим автором робота
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }

      // Пишем в базу
      $products_category = new ProductsCategory;
      $products_category->company_id = $company_id;
      $products_category->author_id = $user_id;

      // Модерация и системная запись
      $products_category->system_item = $request->system_item;
      $products_category->moderation = $request->moderation;

      // Смотрим что пришло
      // Если категория
      if ($request->first_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $products_category_name = $first.$last;
      $products_category->name = $products_category_name;
      $products_category->category_status = 1;
    }

      // Если категория альбомов
    if ($request->medium_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $products_category_name = $first.$last;
      $products_category->name = $products_category_name;
      $products_category->parent_id = $request->parent_id;
    }
    $products_category->save();

    if ($products_category) {
      // Переадресовываем на index
      return redirect()->action('ProductsCategoryController@get_content', ['id' => $products_category->id]);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи сектора!'
      ];
    }
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

    public function edit($id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
      $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $products_category);

      if ($products_category->category_status == 1) {
      // Меняем индустрию
        return view('products_categories.edit-first', ['products_category' => $products_category]);
      } else {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

      // Главный запрос
        $products_categories = ProductsCategory::moderatorLimit($answer)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

      // dd($products_categories);

      // Формируем дерево вложенности
        $products_categories_cat = [];
        foreach ($products_categories as $id => &$node) { 

        // Если нет вложений
          if (!$node['parent_id']) {
            $products_categories_cat[$id] = &$node;
          } else { 

        // Если есть потомки то перебераем массив
            $products_categories[$node['parent_id']]['children'][$id] = &$node;
          };
        };

      // dd($products_categories_cat);

      // Функция отрисовки option'ов
        function tplMenu($products_category, $padding, $parent, $id) {

        // Убираем из списка пришедший пункт меню 
          if ($products_category['id'] != $id) {

            $selected = '';
            if ($products_category['id'] == $parent) {
              $selected = ' selected';
            }
            if ($products_category['category_status'] == 1) {
              $menu = '<option value="'.$products_category['id'].'" class="first"'.$selected.'>'.$products_category['name'].'</option>';
            } else {
              $menu = '<option value="'.$products_category['id'].'"'.$selected.'>'.$padding.' '.$products_category['name'].'</option>';
            }

          // Добавляем пробелы вложенному элементу
            if (isset($products_category['children'])) {
              $i = 1;
              for($j = 0; $j < $i; $j++){
                $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
              }     
              $i++;

              $menu .= showCat($products_category['children'], $padding, $parent, $id);
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
        $products_categories_list = showCat($products_categories_cat, '', $products_category->parent_id, $products_category->id);

        return view('products_categories.edit-medium', ['products_category' => $products_category, 'products_categories_list' => $products_categories_list]);
      }
    }

    public function update(Request $request, $id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

      // ГЛАВНЫЙ ЗАПРОС:
      $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $products_category);

      // Получаем авторизованного пользователя
      $user = $request->user();

      // Модерация и системная запись
      $products_category->system_item = $request->system_item;
      $products_category->moderation = $request->moderation;
      $products_category->parent_id = $request->parent_id;
      $products_category->editor_id = $user->id;

      // Если индустрия
      if ($request->first_item == 1) {
        $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
        $last = mb_substr($request->name,1);//все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $products_category_name = $first.$last;
        $products_category->name = $products_category_name;
      }

      // Если сектор
      if ($request->medium_item == 1) {
        $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
        $last = mb_substr($request->name,1);//все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $products_category_name = $first.$last;
        $products_category->name = $products_category_name;
        
      }

      $products_category->save();

      if ($products_category) {

        // Переадресовываем на index
        return redirect()->action('ProductsCategoryController@get_content', ['id' => $products_category->id]);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи сектора!'
        ];
      }
    }

    public function destroy(Request $request, $id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
      $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $products_category);

    // Удаляем ajax
    // Проверяем содержит ли индустрия вложения
      $products_category_parent = ProductsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

    // Получаем авторизованного пользователя
      $user = $request->user();

    // Если содержит, то даем сообщенеи об ошибке
      if ($products_category_parent) {
      // Если содержит, то даем сообщенеи об ошибке
        $result = [
          'error_status' => 1,
          'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
        ];

      } else {
      // Если нет, мягко удаляем
        if ($products_category->category_status == 1) {
          $parent = null;
        } else {
          $parent = $products_category->parent_id;
        }

        $products_category->editor_id = $user->id;
        $products_category->save();

      // Если нет, мягко удаляем
        $products_category = ProductsCategory::destroy($id);

        if ($products_category) {
        // Переадресовываем на index
          return redirect()->action('ProductsCategoryController@get_content', ['id' => $parent]);
        } else {
          $result = [
            'error_status' => 1,
            'error_message' => 'Ошибка при записи сектора!'
          ];
        }
      }
    }

    // Проверка наличия в базе
    public function products_category_check(Request $request)
    {
      // Получаем авторизованного пользователя
      $user = $request->user();
    // Проверка отдела в нашей базе данных
      $products_category = ProductsCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

    // Если такое название есть
      if ($products_category) {
        $result = [
          'error_status' => 1,
        ];
    // Если нет
      } else {
        $result = [
          'error_status' => 0
        ];
      };
      return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

        // Список категорий альбомов
  public function products_category_list(Request $request)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // Главный запрос
    $products_categories = ProductsCategory::moderatorLimit($answer)
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

    // dd($products_categories);

    // Формируем дерево вложенности
    $products_categories_cat = [];
    foreach ($products_categories as $id => &$node) { 

      // Если нет вложений
      if (!$node['parent_id']) {
        $products_categories_cat[$id] = &$node;
      } else { 

      // Если есть потомки то перебераем массив
        $products_categories[$node['parent_id']]['children'][$id] = &$node;
      };
    };

    // dd($products_categories_cat);

    // Функция отрисовки option'ов
    function tplMenu($products_category, $padding, $parent, $id) {

      // Убираем из списка пришедший пункт меню 
      if ($products_category['id'] != $id) {

        $selected = '';
        if ($products_category['id'] == $parent) {
          $selected = ' selected';
        }
        if ($products_category['category_status'] == 1) {
          $menu = '<option value="'.$products_category['id'].'" class="first"'.$selected.'>'.$products_category['name'].'</option>';
        } else {
          $menu = '<option value="'.$products_category['id'].'"'.$selected.'>'.$padding.' '.$products_category['name'].'</option>';
        }
        
        // Добавляем пробелы вложенному элементу
        if (isset($products_category['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;
          
          $menu .= showCat($products_category['children'], $padding, $parent, $id);
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
    $products_categories_list = showCat($products_categories_cat, '', $request->parent, $request->id);

    // Отдаем ajax
    echo json_encode($products_categories_list, JSON_UNESCAPED_UNICODE);

    // dd($products_categories_list);
  }

  // Сортировка
  public function products_categories_sort(Request $request)
  {
    $result = '';
    $i = 1;
    foreach ($request->products_categories as $item) {

      $products_category = ProductsCategory::findOrFail($item);
      $products_category->sort = $i;
      $products_category->save();
      $i++;
    }
  }
}
