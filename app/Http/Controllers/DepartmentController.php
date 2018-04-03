<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Department;
use App\City;
use App\Position;
use App\Staffer;
use App\Page;
use App\Right;

// Валидация
use App\Http\Requests\DepartmentRequest;

// Политика
use App\Policies\DepartmentPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Session;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'departments';
  protected $entity_dependence = true;

  public function index(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Department::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $departments = Department::with(['staff' => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'staff.position', 'staff.user'])
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    // Дополнительный запрос
    $answer_positions = operator_right('positions', false, 'index');

    //Создаем масив где ключ массива является ID меню
    $departments_rights = [];
    $departments_rights = $departments->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $departments_id = [];
    foreach ($departments_rights as $department) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $department)) {
        $edit = 1;
      };
      if ($user->can('delete', $department)) {
        $delete = 1;
      };
      $department_right = $department->toArray();
      $departments_id[$department_right['id']] = $department_right;
      $departments_id[$department_right['id']]['edit'] = $edit;
      $departments_id[$department_right['id']]['delete'] = $delete;

      // Проверяем прапва на удаление
      foreach ($department->staff as $id => $staffer) {
        $del_staff = 0;
        if ($user->can('delete', $staffer)) {
          $del_staff = 1;
        };
        $departments_id[$department_right['id']]['staff'][$id]['delete'] = $del_staff;
      };
    };

    // dd($departments_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $departments_tree = [];
    foreach ($departments_id as $id => &$node) {   
      //Если нет вложений
      if (!$node['department_parent_id']){
        $departments_tree[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $departments_id[$node['department_parent_id']]  ['children'][$id] = &$node;
      }
    };
    foreach ($departments_tree as $department) {
      $count = 0;
      if (isset($department['children'])) {
        $count = count($department['children']) + $count;
      };
      if (isset($department['staff'])) {
        $count = count($department['staff']) + $count;
      };
      $departments_tree[$department['id']]['count'] = $count;
      // dd($department);
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($departments_tree);
    return view('departments.index', compact('departments_tree', 'positions', 'page_info', 'pages', 'departments'));
  }

  // После записи переходим на созданный пункт меню 
  public function get_content(Request $request)
  {
    // Подключение политики
    $this->authorize('index', Department::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $departments = Department::with(['staff' => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'staff.position', 'staff.user'])
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $departments_rights = [];
    $departments_rights = $departments->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $departments_id = [];
    foreach ($departments_rights->keyBy('id') as $department) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $department)) {
        $edit = 1;
      };
      if ($user->can('delete', $department)) {
        $delete = 1;
      };
      $department_right = $department->toArray();
      $departments_id[$department_right['id']] = $department_right;
      $departments_id[$department_right['id']]['edit'] = $edit;
      $departments_id[$department_right['id']]['delete'] = $delete;

      // Проверяем прапва на удаление
      foreach ($department->staff as $id => $staffer) {
        $del_staff = 0;
        if ($user->can('delete', $staffer)) {
          $del_staff = 1;
        };
        $departments_id[$department_right['id']]['staff'][$id]['delete'] = $del_staff;
      };
    };

    // dd($departments_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $departments_tree = [];
    foreach ($departments_id as $id => &$node) {   
      //Если нет вложений
      if (!$node['department_parent_id']){
        $departments_tree[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $departments_id[$node['department_parent_id']]  ['children'][$id] = &$node;
      }
    };
    foreach ($departments_tree as $department) {
      $count = 0;
      if (isset($department['children'])) {
        $count = count($department['children']) + $count;
      };
      if (isset($department['staff'])) {
        $count = count($department['staff']) + $count;
      };
      $departments_tree[$department['id']]['count'] = $count;
      // dd($department);
    };

    // dd($departments_tree);
    return view('departments.filials-list', ['departments_tree' => $departments_tree, 'item' => $request->item, 'id' => $request->id]); 
  }

  public function create(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Department::class);

    if (isset($request->department_parent_id)) {

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

      $department = Department::moderatorLimit($answer)->where('id', $request->department_parent_id)->first();

      if ($department->filial_status == 1) {
        // Если филиал
        $departments = Department::moderatorLimit($answer)
        ->where('id', $request->department_parent_id)
        ->orWhere('filial_id', $request->department_parent_id)
        ->orderBy('sort', 'asc')
        ->get(['id', 'department_name', 'filial_status', 'department_parent_id'])
        ->keyBy('id')
        ->toArray();

        $filial_id = $department->id;
      } else {
        // Если отдел
        $departments = Department::moderatorLimit($answer)
        ->where('id', $department->filial_id)
        ->orWhere('filial_id', $department->filial_id)
        ->orderBy('sort', 'asc')
        ->get(['id', 'department_name', 'filial_status', 'department_parent_id'])
        ->keyBy('id')
        ->toArray();

        $filial_id = $department->filial_id;
      }
      // dd($departments);

      // Формируем дерево вложенности
      $departments_id = [];
      $departments_cat = [];
      foreach ($departments as $id => &$node) { 

        // Если нет вложений
        if (!$node['department_parent_id']) {
          $departments_cat[$id] = &$node;
        } else { 
        // Если есть потомки то перебераем массив
          $departments[$node['department_parent_id']]['children'][$id] = &$node;
        };
      };

      // echo json_encode($departments_cat);
      // dd($departments_cat);

      // Функция отрисовки option'ов
      function tplMenu($item, $padding, $parent) {

        $selected = '';
        if ($item['id'] == $parent) {
          $selected = ' selected';
        }
        if ($item['filial_status'] == 1) {
          $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['department_name'].'</option>';
        } else {
          $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['department_name'].'</option>';
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
      $departments_list = showCat($departments_cat, '', $request->department_parent_id);

      // echo $departments_list;

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer_staff = operator_right('staff', 'true', 'index');

      // Смотрим на наличие должности в данном филиале, в массиве устанавливаем id должностей, которых не може тбыть более 1ой
      $direction = Staffer::where(['position_id' => 1, 'filial_id' => $filial_id])->moderatorLimit($answer_staff)->count();

      $repeat = [];

      if ($direction == 1) {
        $repeat[] = 1;
      };

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

      // -------------------------------------------------------------------------------------------
      // ГЛАВНЫЙ ЗАПРОС
      // -------------------------------------------------------------------------------------------
      $positions_list = Position::with('staff')->moderatorLimit($answer)
      ->companiesLimit($answer)
      ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer)
      ->systemItem($answer) // Фильтр по системным записям
      ->template($answer) // Выводим шаблоны в список
      ->whereNotIn('id', $repeat)
      ->pluck('position_name', 'id');

      // echo $positions_list;
      // echo $department . ' ' . $positions_list . ' ' . $departments_list;

      return view('departments.create-medium', ['departments_list' => $departments_list, 'positions_list' => $positions_list]);
    } else {

      $department = new Department;

      return view('departments.create-first', ['department' => $department]);
    }
  }

  public function store(DepartmentRequest $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Department::class);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $company_id = $user->company_id;

    if ($user->god == 1) {
      $user_id = 1;
    } else {
      $user_id = $user->id;
    };

    $department = new Department;
    $department->company_id = $company_id;
    $department->city_id = $request->city_id;
   
    // Имя филиала / отдела
    $first = mb_substr($request->department_name,0,1, 'UTF-8'); //первая буква
    $last = mb_substr($request->department_name,1); //все кроме первой буквы
    $first = mb_strtoupper($first, 'UTF-8');
    $department->department_name = $first.$last;

    $department->address = $request->address;
    if (isset($request->phone)) {
      $department->phone = cleanPhone($request->phone);
    }
    
    $department->author_id = $user_id;

    // Пишем филиал
    if ($request->first_item == 1) {
      $department->filial_status = 1;
    };

    // Пишем отделы
    if ($request->medium_item == 1) {
      $department->filial_id = $request->filial_id;
      $department->department_parent_id = $request->department_parent_id;
    };

    $department->save();



    if($department){

      if($department->filial_status == 1) {

        // Создаем папку в файловой системе
        $link_for_folder = 'public/companies/' . $company_id . '/'. $department->id . '/users';
        Storage::makeDirectory($link_for_folder);

        $link_for_folder = 'public/companies/' . $company_id . '/'. $department->id . '/sites';
        Storage::makeDirectory($link_for_folder);

        $link_for_folder = 'public/companies/' . $company_id . '/'. $department->id . '/goods';
        Storage::makeDirectory($link_for_folder);

        $link_for_folder = 'public/companies/' . $company_id . '/'. $department->id . '/documents';
        Storage::makeDirectory($link_for_folder);

        // $link = 'departments';
        // return redirect($link);
      }

      // Переадресовываем на index
      return redirect()->action('DepartmentController@get_content', ['id' => $department->id, 'item' => 'department']);

      // $action_method = "DepartmentController@get_content";
      // $action_arrray = ['id' => $department->id, 'item' => 'department'];

      // return redirect()->action('GetAccessController@set', ['action_method' => $action_method, 'action_arrray' => $action_arrray]);
      
    } else {
      abort(403, 'Ошибка при записи отдела!');
    };
  }

  public function show($id)
  {

  }

  public function edit($id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $department = Department::with('city')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $department);

    if ($department->filial_status == 1) {
      // Меняем филиал
      return view('departments.edit-first', ['department' => $department]);
    } else {
      // Меняем отдел
      $item_id = $department->id;
      $filial_id = $department->filial_id;
      $parent_id = $department->department_parent_id;

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

      // Главный запрос
      $departments = Department::moderatorLimit($answer)
      ->where('id', $filial_id)
      ->orWhere('filial_id', $filial_id)
      ->orderBy('sort', 'asc')
      ->get(['id', 'department_name', 'filial_status', 'department_parent_id'])
      ->keyBy('id')
      ->toArray();

      // echo $departments;

      // Формируем дерево вложенности
      $departments_id = [];
      $departments_cat = [];

      foreach ($departments as $id => &$node) { 
        // Если нет вложений
        if (!$node['department_parent_id']) {
          $departments_cat[$id] = &$node;
        } else { 
        // Если есть потомки то перебераем массив
          $departments[$node['department_parent_id']]['children'][$id] = &$node;
        };
      };

      // echo json_encode($departments_cat, JSON_UNESCAPED_UNICODE);

      // Функция отрисовки option'ов
      function tplMenu($item, $padding, $id, $parent) {
        // echo json_encode($item, JSON_UNESCAPED_UNICODE);
        // Убираем из списка пришедший отдел 
        if ($item['id'] != $id) {

          $selected = '';
          if ($item['id'] == $parent) {
            $selected = ' selected';
          }
          if ($item['filial_status'] == 1) {
            $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['department_name'].'</option>';
          } else {
            $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['department_name'].'</option>';
          }

        // Добавляем пробелы вложенному элементу
          if (isset($item['children'])) {
            $i = 1;
            for($j = 0; $j < $i; $j++){
              $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }     
            $i++;

            $menu .= showCat($item['children'], $padding, $id, $parent);
          }
          return $menu;
        }
      }

      // Рекурсивно считываем наш шаблон
      function showCat($data, $padding, $id, $parent){
        $string = '';
        $padding = $padding;

        foreach($data as $item){
          $string .= tplMenu($item, $padding, $id, $parent);
        }
        return $string;
      }

      // echo $item_id . ' ' . json_encode($departments_cat, JSON_UNESCAPED_UNICODE);

      // echo $parent_id;
      // Получаем HTML разметку
      $departments_list = showCat($departments_cat, '', $item_id, $parent_id);

      // echo json_encode($departments_list);
      // echo $department . ' ' . $departments_list;
      return view('departments.edit-medium', ['department' => $department, 'departments_list' => $departments_list]);
    } 
  }

  public function update(DepartmentRequest $request, $id)
  {
    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__))
    ;
    // ГЛАВНЫЙ ЗАПРОС:
    $department = Department::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $department);

    if ($user->god == 1) {
      $user_id = 1;
    } else {
      $user_id = $user->id;
    }

    $department->city_id = $request->city_id;

    // Имя филиала / отдела
    $first = mb_substr($request->department_name,0,1, 'UTF-8'); //первая буква
    $last = mb_substr($request->department_name,1); //все кроме первой буквы
    $first = mb_strtoupper($first, 'UTF-8');
    $department->department_name = $first.$last;

    $department->address = $request->address;
    if (isset($request->phone)) {
      $department->phone = cleanPhone($request->phone);
    }

    $department->editor_id = $user_id;

    if ($request->medium_item == 1) {
      $department->department_parent_id = $request->department_parent_id;
    }

    $department->save();
    if ($department) {
      // Переадресовываем на index
      return redirect()->action('DepartmentController@get_content', ['id' => $department->id, 'item' => 'department']);
      } else {
        abort(403, 'Ошибка при обновлении отдела!');
      };
  }

  public function destroy(Request $request, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $department = Department::with('staff')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $department);

    // Удаляем ajax
    // Проверяем содержит ли филиал / отдел вложения / должности
    $department_parent = Department::moderatorLimit($answer)->whereDepartment_parent_id($id)->first();

    // Получаем авторизованного пользователя
    $user = $request->user();
   


    if (isset($department_parent) || (count($department->staff) > 0)) {

      $result = [
        'error_status' => 1,
        'error_message' => 'Данный отдел не пуст, удаление невозможно'
      ];
    } else {
      // Если нет, мягко удаляем
      if ($department->filial_status == 1) {
        $parent = null;
      } else {
        $parent = $department->department_parent_id;
      }

      $department->editor_id = $user->id;
      $department->save();

      $department = Department::destroy($id); 

      if ($department) {
        // Переадресовываем на index
        return redirect()->action('DepartmentController@get_content', ['id' => $parent, 'item' => 'department']);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при удалении!'
        ];
      }
    }
  }

  public function departments_list(Request $request)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, 'index');

    $departments_list = Department::moderatorLimit($answer)->whereId($request->filial_id)
    ->orWhere('filial_id', $request->filial_id)
    ->pluck('department_name', 'id');
    echo json_encode($departments_list, JSON_UNESCAPED_UNICODE);
  }

  public function department_check(Request $request)
  {
    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверка отдела в нашей базе данных
    $department = Department::where(['department_name' => $request->name, 'company_id' => $user->company_id])->first();

    $res = false;
    if ($department) {
      // Если такой филиал существует в компании
      if (isset($department->filial_status)) {
        $res = true;
      };
      if ($department->filial_id == $request->filial_id) {
        $res = true;
      }
    }
    
    if ($res) {
      $result = [
        'error_message' => 'Такой отдел уже существует',
        'error_status' => 1,
      ];
    } else {
      $result = [
        'error_status' => 0,
      ];
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  public function departments_sort(Request $request)
  {

    if (isset($request->departments)) {
      $i = 1;
      foreach ($request->departments as $item) {

        $department = Department::findOrFail($item);
        $department->sort = $i;
        $department->save();

        $i++;
      }
    }
    if (isset($request->staff)) {
      $i = 1;
      foreach ($request->staff as $item) {

        $staffer = Staffer::findOrFail($item);
        $staffer->sort = $i;
        $staffer->save();

        $i++;
      }
    }
  }
}