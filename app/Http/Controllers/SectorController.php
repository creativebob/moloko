<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Sector;

// Валидация
use App\Http\Requests\SectorRequest;

// Политика
use App\Policies\SectorPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'sectors';
  protected $entity_dependence = false;

  public function index(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Sector::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $sectors = Sector::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->booklistFilter($request)
    ->orderBy('sort', 'asc')
    ->get();

    // Запрос для фильтра
    $filter_query = Sector::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->get();

    $filter['status'] = null;

    // Добавляем данные по спискам (Требуется на каждом контроллере)
    $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);
    // dd($filter);

    // Создаем масив где ключ массива является ID меню
    $sectors_rights = [];
    $sectors_rights = $sectors->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $sectors_id = [];
    foreach ($sectors_rights as $sector) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $sector)) {
        $edit = 1;
      };
      if ($user->can('delete', $sector)) {
        $delete = 1;
      };
      $sector_right = $sector->toArray();
      $sectors_id[$sector_right['id']] = $sector_right;
      $sectors_id[$sector_right['id']]['edit'] = $edit;
      $sectors_id[$sector_right['id']]['delete'] = $delete;
    };

    // dd($sectors_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $sectors_tree = [];
    foreach ($sectors_id as $id => &$node) {   
      // Если нет вложений
      if (!$node['parent_id']){
        $sectors_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $sectors_id[$node['parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($sectors_tree as $sector) {
      $count = 0;
      if (isset($sector['children'])) {
        $count = count($sector['children']) + $count;
      };
      $sectors_tree[$sector['id']]['count'] = $count;
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($sectors_tree);

    // Отдаем Ajax
    if($request->ajax()) {
      return view('sectors.industry-list', ['sectors_tree' => $sectors_tree, 'id' => $request->id]);
    }
    // Отдаем на шаблон
    return view('sectors.index', compact('sectors_tree', 'page_info', 'filter'));
  }

  public function get_content(Request $request)
  {
    // Политика
    $this->authorize(getmethod('index'), Sector::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $sectors = Sector::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $sectors_rights = [];
    $sectors_rights = $sectors->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $sectors_id = [];
    foreach ($sectors_rights as $sector) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $sector)) {
        $edit = 1;
      };
      if ($user->can('delete', $sector)) {
        $delete = 1;
      };
      $sector_right = $sector->toArray();
      $sectors_id[$sector_right['id']] = $sector_right;
      $sectors_id[$sector_right['id']]['edit'] = $edit;
      $sectors_id[$sector_right['id']]['delete'] = $delete;
    };

    // dd($sectors_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $sectors_tree = [];
    foreach ($sectors_id as $id => &$node) {   
      // Если нет вложений
      if (!$node['parent_id']){
        $sectors_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $sectors_id[$node['parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($sectors_tree as $sector) {
      $count = 0;
      if (isset($sector['children'])) {
        $count = count($sector['children']) + $count;
      };
      $sectors_tree[$sector['id']]['count'] = $count;
    };

    
    // Отдаем Ajax
    return view('sectors.category-list', ['sectors_tree' => $sectors_tree, 'id' => $request->id]);
  }

  public function create(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Sector::class);

    $sector = new Sector;

    if (isset($request->parent_id)) {

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

      // Главный запрос
      $sectors = Sector::moderatorLimit($answer)
      ->orderBy('sort', 'asc')
      ->get(['id','name','category_status','parent_id'])
      ->keyBy('id')
      ->toArray();

      // dd($sectors);

      // Формируем дерево вложенности
      $sectors_cat = [];
      foreach ($sectors as $id => &$node) { 

        // Если нет вложений
        if (!$node['parent_id']) {
          $sectors_cat[$id] = &$node;
        } else { 

        // Если есть потомки то перебераем массив
          $sectors[$node['parent_id']]['children'][$id] = &$node;
        };
      };

      // dd($sectors_cat);

      // Функция отрисовки option'ов
      function tplMenu($sector, $padding, $parent) {

        $selected = '';
        if ($sector['id'] == $parent) {
          $selected = ' selected';
        }
        if ($sector['category_status'] == 1) {
          $menu = '<option value="'.$sector['id'].'" class="first"'.$selected.'>'.$sector['name'].'</option>';
        } else {
          $menu = '<option value="'.$sector['id'].'"'.$selected.'>'.$padding.' '.$sector['name'].'</option>';
        }
        
        // Добавляем пробелы вложенному элементу
        if (isset($sector['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;
          
          $menu .= showCat($sector['children'], $padding, $parent);
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
      $sectors_list = showCat($sectors_cat, '', $request->parent_id);

      // echo $sectors_list;


      return view('sectors.create-medium', ['sector' => $sector, 'sectors_list' => $sectors_list]);
    } else {
      return view('sectors.create-first', ['sector' => $sector]);
    }
  }

  public function store(SectorRequest $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Sector::class);

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
    $sector = new Sector;
    $sector->company_id = $company_id;
    $sector->author_id = $user_id;

    // Модерация и системная запись
    $sector->system_item = $request->system_item;
    $sector->moderation = $request->moderation;
    $sector->parent_id = $request->parent_id;

    // Смотрим что пришло
    // Если индустрия
    if ($request->first_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $sector_name = $first.$last;
      $sector->name = $sector_name;
      $sector->category_status = 1;
    }

    // Если сектор
    if ($request->medium_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $sector_name = $first.$last;
      $sector->name = $sector_name;
    }
    $sector->save();

    if ($sector) {
      // Переадресовываем на index
      return redirect()->action('SectorController@get_content', ['id' => $sector->id]);
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

  public function edit($id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $sector);

    if ($sector->category_status == 1) {
      // Меняем индустрию
      return view('sectors.edit-first', ['sector' => $sector]);
    } else {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

      // Главный запрос
      $sectors = Sector::moderatorLimit($answer)
      ->orderBy('sort', 'asc')
      ->get(['id','name','category_status','parent_id'])
      ->keyBy('id')
      ->toArray();

      // dd($sectors);

      // Формируем дерево вложенности
      $sectors_cat = [];
      foreach ($sectors as $id => &$node) { 

        // Если нет вложений
        if (!$node['parent_id']) {
          $sectors_cat[$id] = &$node;
        } else { 

        // Если есть потомки то перебераем массив
          $sectors[$node['parent_id']]['children'][$id] = &$node;
        };
      };

      // dd($sectors_cat);

      // Функция отрисовки option'ов
      function tplMenu($sector, $padding, $parent, $id) {

        // Убираем из списка пришедший пункт меню 
        if ($sector['id'] != $id) {

          $selected = '';
          if ($sector['id'] == $parent) {
            $selected = ' selected';
          }
          if ($sector['category_status'] == 1) {
            $menu = '<option value="'.$sector['id'].'" class="first"'.$selected.'>'.$sector['name'].'</option>';
          } else {
            $menu = '<option value="'.$sector['id'].'"'.$selected.'>'.$padding.' '.$sector['name'].'</option>';
          }
          
          // Добавляем пробелы вложенному элементу
          if (isset($sector['children'])) {
            $i = 1;
            for($j = 0; $j < $i; $j++){
              $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }     
            $i++;
            
            $menu .= showCat($sector['children'], $padding, $parent, $id);
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
      $sectors_list = showCat($sectors_cat, '', $sector->parent_id, $sector->id);



      return view('sectors.edit-medium', ['sector' => $sector, 'sectors_list' => $sectors_list]);
    };
  }

  public function update(SectorRequest $request, $id)
  {    
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $sector);

    // Получаем авторизованного пользователя
    $user = $request->user();

    // Модерация и системная запись
    $sector->system_item = $request->system_item;
    $sector->moderation = $request->moderation;
    $sector->parent_id = $request->parent_id;
    $sector->editor_id = $user->id;
    
    // Если индустрия
    if ($request->first_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $sector_name = $first.$last;
      $sector->name = $sector_name;
    }

    // Если сектор
    if ($request->medium_item == 1) {
      $first = mb_substr($request->name,0,1, 'UTF-8');//первая буква
      $last = mb_substr($request->name,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $last = mb_strtolower($last, 'UTF-8');
      $sector_name = $first.$last;
      $sector->name = $sector_name;
    }

    $sector->save();

    if ($sector) {
      // Переадресовываем на index
      return redirect()->action('SectorController@get_content', ['id' => $sector->id]);
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
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);
    
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $sector);

    // Удаляем ajax
    // Проверяем содержит ли индустрия вложения
    $sector_parent = Sector::moderatorLimit($answer)->whereParent_id($id)->first();

    // Получаем авторизованного пользователя
    $user = $request->user();

    // Если содержит, то даем сообщенеи об ошибке
    if ($sector_parent) {
      // Если содержит, то даем сообщенеи об ошибке
      $result = [
        'error_status' => 1,
        'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
      
    } else {
      // Если нет, мягко удаляем
      if ($sector->category_status == 1) {
        $parent = null;
      } else {
        $parent = $sector->parent_id;
      }

      $sector->editor_id = $user->id;
      $sector->save();

      // Если нет, мягко удаляем
      $sector = Sector::destroy($id);

      if ($sector) {
        // Переадресовываем на index
        return redirect()->action('SectorController@get_content', ['id' => $parent]);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи сектора!'
        ];
      }
    }
  }

  // Проверка наличия в базе
  public function sector_check(Request $request)
  {
    // Проверка отдела в нашей базе данных
    $sector = Sector::where('name', $request->name)->first();

    // Если такое название есть
    if ($sector) {
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

  // Список секторов
  public function sectors_list(Request $request)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // Главный запрос
    $sectors = Sector::moderatorLimit($answer)
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

    // dd($sectors);

    // Формируем дерево вложенности
    $sectors_cat = [];
    foreach ($sectors as $id => &$node) { 

      // Если нет вложений
      if (!$node['parent_id']) {
        $sectors_cat[$id] = &$node;
      } else { 

      // Если есть потомки то перебераем массив
        $sectors[$node['parent_id']]['children'][$id] = &$node;
      };
    };

    // dd($sectors_cat);

    // Функция отрисовки option'ов
    function tplMenu($sector, $padding, $parent, $id) {

      // Убираем из списка пришедший пункт меню 
      if ($sector['id'] != $id) {

        $selected = '';
        if ($sector['id'] == $parent) {
          $selected = ' selected';
        }
        if ($sector['category_status'] == 1) {
          $menu = '<option value="'.$sector['id'].'" class="first"'.$selected.'>'.$sector['name'].'</option>';
        } else {
          $menu = '<option value="'.$sector['id'].'"'.$selected.'>'.$padding.' '.$sector['name'].'</option>';
        }
        
        // Добавляем пробелы вложенному элементу
        if (isset($sector['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;
          
          $menu .= showCat($sector['children'], $padding, $parent, $id);
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
    $sectors_list = showCat($sectors_cat, '', $request->parent, $request->id);

    // Отдаем ajax
    echo json_encode($sectors_list, JSON_UNESCAPED_UNICODE);

    // dd($sectors_list);
  }

  // Сортировка
  public function sectors_sort(Request $request)
  {
    $result = '';
    $i = 1;
    foreach ($request->sectors as $item) {

      $sector = Sector::findOrFail($item);
      $sector->sort = $i;
      $sector->save();

      // if ($sector) {
      //   $result = [
      //     'error_status' => 0,
      //   ];
      // } else {
      //   $result = [
      //     'error_status' => 1,
      //     'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
      //   ];
      // }
      // return json_encode($result, JSON_UNESCAPED_UNICODE);

      $i++;
    }
  }
}
