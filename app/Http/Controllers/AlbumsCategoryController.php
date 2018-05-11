<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\AlbumsCategory;

// Валидация
use App\Http\Requests\AlbumsCategoryRequest;

// Политика
use App\Policies\AlbumsCategoryPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumsCategoryController extends Controller
{
    // Сущность над которой производит операции контроллер
  protected $entity_name = 'albums_categories';
  protected $entity_dependence = false;


  public function index(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));


    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $albums_categories = AlbumsCategory::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $albums_categories_rights = [];
    $albums_categories_rights = $albums_categories->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $albums_categories_id = [];
    foreach ($albums_categories_rights as $albums_category) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $albums_category)) {
        $edit = 1;
      };
      if ($user->can('delete', $albums_category)) {
        $delete = 1;
      };
      $albums_category_right = $albums_category->toArray();
      $albums_categories_id[$albums_category_right['id']] = $albums_category_right;
      $albums_categories_id[$albums_category_right['id']]['edit'] = $edit;
      $albums_categories_id[$albums_category_right['id']]['delete'] = $delete;
    };

    // dd($albums_categories_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $albums_categories_tree = [];
    foreach ($albums_categories_id as $id => &$node) {   
      // Если нет вложений
      if (!$node['parent_id']){
        $albums_categories_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $albums_categories_id[$node['parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($albums_categories_tree as $albums_category) {
      $count = 0;
      if (isset($albums_category['children'])) {
        $count = count($albums_category['children']) + $count;
      };
      $albums_categories_tree[$albums_category['id']]['count'] = $count;
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($albums_categories_tree);

    // Отдаем Ajax
    if($request->ajax()) {
      return view('albums_categories.category-list', ['albums_categories_tree' => $albums_categories_tree, 'id' => $request->id]);
    }
    // Отдаем на шаблон
    return view('albums_categories.index', compact('albums_categories_tree', 'page_info'));
  }

  public function get_content(Request $request)
  {
    // Политика
    $this->authorize(getmethod('index'), AlbumsCategory::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $albums_categories = AlbumsCategory::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $albums_categories_rights = [];
    $albums_categories_rights = $albums_categories->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $albums_categories_id = [];
    foreach ($albums_categories_rights as $albums_category) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $albums_category)) {
        $edit = 1;
      };
      if ($user->can('delete', $albums_category)) {
        $delete = 1;
      };
      $albums_category_right = $albums_category->toArray();
      $albums_categories_id[$albums_category_right['id']] = $albums_category_right;
      $albums_categories_id[$albums_category_right['id']]['edit'] = $edit;
      $albums_categories_id[$albums_category_right['id']]['delete'] = $delete;
    };

    // dd($albums_categories_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $albums_categories_tree = [];
    foreach ($albums_categories_id as $id => &$node) {   
      // Если нет вложений
      if (!$node['parent_id']){
        $albums_categories_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $albums_categories_id[$node['parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($albums_categories_tree as $albums_category) {
      $count = 0;
      if (isset($albums_category['children'])) {
        $count = count($albums_category['children']) + $count;
      };
      $albums_categories_tree[$albums_category['id']]['count'] = $count;
    };

    // Отдаем Ajax
    return view('albums_categories.category-list', ['albums_categories_tree' => $albums_categories_tree, 'id' => $request->id]);
  }
  public function create(Request $request)
  {
      // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

    $albums_category = new AlbumsCategory;

    if (isset($request->parent_id)) {

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

      // Главный запрос
      $albums_categories = AlbumsCategory::moderatorLimit($answer)
      ->orderBy('sort', 'asc')
      ->get(['id','name','category_status','parent_id'])
      ->keyBy('id')
      ->toArray();

      // dd($albums_categories);

      // Формируем дерево вложенности
      $albums_categories_cat = [];
      foreach ($albums_categories as $id => &$node) { 

        // Если нет вложений
        if (!$node['parent_id']) {
          $albums_categories_cat[$id] = &$node;
        } else { 

        // Если есть потомки то перебераем массив
          $albums_categories[$node['parent_id']]['children'][$id] = &$node;
        };
      };

      // dd($albums_categories_cat);

      // Функция отрисовки option'ов
      function tplMenu($albums_category, $padding, $parent) {

        $selected = '';
        if ($albums_category['id'] == $parent) {
          $selected = ' selected';
        }
        if ($albums_category['category_status'] == 1) {
          $menu = '<option value="'.$albums_category['id'].'" class="first"'.$selected.'>'.$albums_category['name'].'</option>';
        } else {
          $menu = '<option value="'.$albums_category['id'].'"'.$selected.'>'.$padding.' '.$albums_category['name'].'</option>';
        }
        
        // Добавляем пробелы вложенному элементу
        if (isset($albums_category['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;
          
          $menu .= showCat($albums_category['children'], $padding, $parent);
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
      $albums_categories_list = showCat($albums_categories_cat, '', $request->parent_id);

      // echo $albums_categories_list;


      return view('albums_categories.create-medium', ['albums_category' => $albums_category, 'albums_categories_list' => $albums_categories_list]);
    } else {
      return view('albums_categories.create-first', ['albums_category' => $albums_category]);
    }
  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AlbumsCategoryRequest $request)
    {
      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

      // Получаем данные для авторизованного пользователя
      $user = $request->user();

      // Смотрим компанию пользователя
      $company_id = $user->company_id;
      if($company_id == null) {
        abort(403, 'Необходимо авторизоваться под компанией');
      }

      // Скрываем бога
      $user_id = hideGod($user);

      // Пишем в базу
      $albums_category = new AlbumsCategory;
      $albums_category->company_id = $company_id;
      $albums_category->author_id = $user_id;

      // Модерация и системная запись
      $albums_category->system_item = $request->system_item;
      $albums_category->moderation = $request->moderation;

      // Смотрим что пришло
      // Если категория
      if ($request->first_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $albums_category_name = $first.$last;
      $albums_category->name = $albums_category_name;
      $albums_category->category_status = 1;
    }

      // Если категория альбомов
    if ($request->medium_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $albums_category_name = $first.$last;
      $albums_category->name = $albums_category_name;
      $albums_category->parent_id = $request->parent_id;
    }
    $albums_category->save();

    if ($albums_category) {
      // Переадресовываем на index
      return redirect()->action('AlbumsCategoryController@get_content', ['id' => $albums_category->id]);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

      // ГЛАВНЫЙ ЗАПРОС:
      $albums_category = AlbumsCategory::moderatorLimit($answer)->findOrFail($id);

      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $albums_category);

      if ($albums_category->category_status == 1) {
        // Меняем индустрию
        return view('albums_categories.edit-first', ['albums_category' => $albums_category]);
      } else {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($albums_categories);

        // Формируем дерево вложенности
        $albums_categories_cat = [];
        foreach ($albums_categories as $id => &$node) { 

        // Если нет вложений
          if (!$node['parent_id']) {
            $albums_categories_cat[$id] = &$node;
          } else { 

        // Если есть потомки то перебераем массив
            $albums_categories[$node['parent_id']]['children'][$id] = &$node;
          };
        };

      // dd($albums_categories_cat);

      // Функция отрисовки option'ов
        function tplMenu($albums_category, $padding, $parent, $id) {

        // Убираем из списка пришедший пункт меню 
          if ($albums_category['id'] != $id) {

            $selected = '';
            if ($albums_category['id'] == $parent) {
              $selected = ' selected';
            }
            if ($albums_category['category_status'] == 1) {
              $menu = '<option value="'.$albums_category['id'].'" class="first"'.$selected.'>'.$albums_category['name'].'</option>';
            } else {
              $menu = '<option value="'.$albums_category['id'].'"'.$selected.'>'.$padding.' '.$albums_category['name'].'</option>';
            }

          // Добавляем пробелы вложенному элементу
            if (isset($albums_category['children'])) {
              $i = 1;
              for($j = 0; $j < $i; $j++){
                $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
              }     
              $i++;

              $menu .= showCat($albums_category['children'], $padding, $parent, $id);
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
        $albums_categories_list = showCat($albums_categories_cat, '', $albums_category->parent_id, $albums_category->id);

        return view('albums_categories.edit-medium', ['albums_category' => $albums_category, 'albums_categories_list' => $albums_categories_list]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AlbumsCategoryRequest $request, $id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

      // ГЛАВНЫЙ ЗАПРОС:
      $albums_category = AlbumsCategory::moderatorLimit($answer)->findOrFail($id);

      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $albums_category);

      // Получаем авторизованного пользователя
      $user = $request->user();

      // Модерация и системная запись
      $albums_category->system_item = $request->system_item;
      $albums_category->moderation = $request->moderation;
      $albums_category->parent_id = $request->parent_id;
      $albums_category->editor_id = $user_id;

      // Если индустрия
      if ($request->first_item == 1) {
        $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
        $last = mb_substr($request->name,1);//все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $albums_category_name = $first.$last;
        $albums_category->name = $albums_category_name;
      }

      // Если сектор
      if ($request->medium_item == 1) {
        $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
        $last = mb_substr($request->name,1);//все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $albums_category_name = $first.$last;
        $albums_category->name = $albums_category_name;
        
      }

      $albums_category->save();

      if ($albums_category) {

        // Переадресовываем на index
        return redirect()->action('AlbumsCategoryController@get_content', ['id' => $albums_category->id]);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи сектора!'
        ];
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
      $albums_category = AlbumsCategory::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $albums_category);

    // Удаляем ajax
    // Проверяем содержит ли индустрия вложения
      $albums_category_parent = AlbumsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

    // Получаем авторизованного пользователя
      $user = $request->user();

    // Если содержит, то даем сообщенеи об ошибке
      if ($albums_category_parent) {
      // Если содержит, то даем сообщенеи об ошибке
        $result = [
          'error_status' => 1,
          'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
        ];

      } else {
      // Если нет, мягко удаляем
        if ($albums_category->category_status == 1) {
          $parent = null;
        } else {
          $parent = $albums_category->parent_id;
        }

        $albums_category->editor_id = $user->id;
        $albums_category->save();

      // Если нет, мягко удаляем
        $albums_category = AlbumsCategory::destroy($id);

        if ($albums_category) {
        // Переадресовываем на index
          return redirect()->action('AlbumsCategoryController@get_content', ['id' => $parent]);
        } else {
          $result = [
            'error_status' => 1,
            'error_message' => 'Ошибка при записи сектора!'
          ];
        }
      }
    }

    // Проверка наличия в базе
    public function albums_category_check(Request $request)
    {
      // Получаем авторизованного пользователя
      $user = $request->user();
    // Проверка отдела в нашей базе данных
      $albums_category = AlbumsCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

    // Если такое название есть
      if ($albums_category) {
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
    public function albums_category_list(Request $request)
    {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // Главный запрос
      $albums_categories = AlbumsCategory::moderatorLimit($answer)
      ->get(['id','name','category_status','parent_id'])
      ->keyBy('id')
      ->toArray();

    // dd($albums_categories);

    // Формируем дерево вложенности
      $albums_categories_cat = [];
      foreach ($albums_categories as $id => &$node) { 

      // Если нет вложений
        if (!$node['parent_id']) {
          $albums_categories_cat[$id] = &$node;
        } else { 

      // Если есть потомки то перебераем массив
          $albums_categories[$node['parent_id']]['children'][$id] = &$node;
        };
      };

    // dd($albums_categories_cat);

    // Функция отрисовки option'ов
      function tplMenu($albums_category, $padding, $parent, $id) {

      // Убираем из списка пришедший пункт меню 
        if ($albums_category['id'] != $id) {

          $selected = '';
          if ($albums_category['id'] == $parent) {
            $selected = ' selected';
          }
          if ($albums_category['category_status'] == 1) {
            $menu = '<option value="'.$albums_category['id'].'" class="first"'.$selected.'>'.$albums_category['name'].'</option>';
          } else {
            $menu = '<option value="'.$albums_category['id'].'"'.$selected.'>'.$padding.' '.$albums_category['name'].'</option>';
          }

        // Добавляем пробелы вложенному элементу
          if (isset($albums_category['children'])) {
            $i = 1;
            for($j = 0; $j < $i; $j++){
              $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }     
            $i++;

            $menu .= showCat($albums_category['children'], $padding, $parent, $id);
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
      $albums_categories_list = showCat($albums_categories_cat, '', $request->parent, $request->id);

    // Отдаем ajax
      echo json_encode($albums_categories_list, JSON_UNESCAPED_UNICODE);

    // dd($albums_categories_list);
    }

  // Сортировка
    public function albums_categories_sort(Request $request)
    {
      $result = '';
      $i = 1;
      foreach ($request->albums_categories as $item) {

        $albums_category = AlbumsCategory::findOrFail($item);
        $albums_category->sort = $i;
        $albums_category->save();
        $i++;
      }
    }
  }
