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

class DepartmentController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'departments';
  protected $entity_dependence = true;

  public function index(Request $request)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Department::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $departments = Department::with(['staff', 'staff.position', 'staff.user'])
    ->withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('moderated', 'desc')
    ->get();

    $answer_positions = operator_right('positions', false, $method);
    // dd($answer);
    // Получаем список должностей
    $positions_list = Position::withoutGlobalScope($answer_positions['moderator'])
    ->moderatorFilter($answer_positions)
    ->companiesFilter($answer_positions)
    ->filials($answer_positions) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_positions)
    ->systemItem($answer_positions) // Фильтр по системным записям
    ->pluck('position_name', 'id');
    // dd($departments);
    // dd($departments);
    //Создаем масив где ключ массива является ID меню
    $departments_rights = [];
    $departments_rights = $departments->keyBy('id');
    // foreach ($departments as $department) {
    //   $departments_rights[$department['id']] = $department;
    // };
    // dd($departments_rights);
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
          
    $departments_list = $departments->pluck('department_name', 'id');
    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    // dd($departments_tree);
    return view('departments', compact('departments_tree', 'positions', 'positions_list', 'departments_list', 'page_info', 'pages', 'departments'));
  }

  // Получаем сторонние данные по 
  public function current_department(Request $request, $section_id, $item_id)
  {
    // Получаем метод
    $method = 'index';
    // Подключение политики
    $this->authorize($method, Department::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $departments = Department::with(['staff', 'staff.position', 'staff.user'])
    ->withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('moderated', 'desc')
    ->get();
    // Получаем список должностей
    $positions_list = Position::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->pluck('position_name', 'id');
    // dd($departments);
    //Создаем масив где ключ массива является ID меню
    $departments_rights = [];
    foreach ($departments as $department) {
      $departments_rights[$department['id']] = $department;
    };
    // dd($departments_rights);
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
        $departments_id[$node['department_parent_id']]['children'][$id] = &$node;
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
    $departments_list = $departments->pluck('department_name', 'id');
    // Инфо о странице
    $page_info = pageInfo('departments');
    $data = [
      'section_name' => 'departments',
      'item_name' => 'staff',
      'section_id' => $section_id,
      'item_id' => $item_id,
    ];
    return view('departments', compact('departments_tree', 'positions', 'positions_list', 'data', 'departments_list', 'staff', 'page_info', 'departments')); 
  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    // dd($request);
    // Получаем метод
    $method = 'create';
    // Подключение политики
    $this->authorize($method, Department::class);
    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;
    // Пишем филиал
    if (isset($request->filial_database)) {
      // Если город найден, то меняем значение на 1, пишем в базу и отдаем результат
      if ($request->filial_database == 1) {
        // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;
        $filial = new Department;
        $filial->company_id = $company_id;
        $filial->city_id = $request->city_id;
        $filial->department_name = $request->filial_name;
        $filial->department_address = $request->filial_address;
        $filial->department_phone = cleanPhone($request->filial_phone);
        $filial->filial_status = 1;
        // $filial->author_id = $user_id;
        $filial->save();
        if($filial) {
          return Redirect('/current_department/'.$filial->id.'/0');
        } else {
          abort(403, 'Ошибка при записи филиала!');
        };
        
      };
    };
    // Пишем отделы
    if (isset($request->department_database)) {
      if ($request->department_database == 0) {
        // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;
        // Проверка отдела в нашей базе данных
        $department_name = $request->department_name;
        $filial_id = $request->filial_id;
        $result = Department::where([
          ['department_name', 'like', $department_name.'%'],
          ['filial_id', '=', $filial_id],
        ])->first();
        if ($result) {
          $result = [
            'error_message' => 'Такой отдел уже существует',
            'error_status' => 0,
          ];
        } else {
          $result = [
            'error_status' => 1
          ];
        };
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      };
      // Если отдел не найден, то меняем значение на 1, пишем в базу и отдаем результат
      if ($request->department_database == 1) {
        $department = new Department;
        $department->company_id = $company_id;
        $department->department_name = $request->department_name;
        if($request->department_address == '') {
        } else {
          $department->department_address = $request->department_address;
        };
        if($request->department_phone == '') {
        } else {
          $department->department_phone = cleanPhone($request->department_phone);
        };
        $department->filial_id = $request->filial_id;
        $department->department_parent_id = $request->parent_id;
        $department->author_id = $user_id;
        $department->save();
        $department_id = $department->id;
        if($department){
          return Redirect('/current_department/'.$request->filial_id.'/'.$department_id);
        } else {
          abort(403, 'Ошибка при записи отдела!');
        };
      };
    };
  }

  public function show($id)
  {
    
  }

  public function edit($id)
  {
    // Получаем метод
    $method = 'update';
    // ГЛАВНЫЙ ЗАПРОС:
   $department = Department::with('city')->withoutGlobalScope(ModerationScope::class)->findOrFail($id);
    // Подключение политики
    $this->authorize($method, $department);
    if ($department->filial_status == 1) {
      // Меняем филиал
      $result = [
        'city_id' => $department->city_id,
        'city_name' => $department->city->city_name,
        'filial_name' => $department->department_name,
        'filial_address' => $department->department_address,
        'filial_phone' => decorPhone($department->department_phone),
      ];
    } else {
      // Меняем отдел
      if (isset($department->city_id)) {
        $city_id = $department->city_id; 
      } else {
        $city_id = '';
      };
      if (isset($department->city->city_name)) {
        $city_name = $department->city->city_name;
      } else {
        $city_name = '';
      };
      if (isset($department->department_address)) {
        $department_address = $department->department_address;
      } else {
        $department_address = '';
      };
      if (isset($department->department_phone)) {
        $department_phone = decorPhone($department->department_phone);
      } else {
        $department_phone = '';
      };
      $result = [
        'city_id' => $city_id,
        'city_name' => $city_name,
        'department_address' => $department_address,
        'department_phone' => $department_phone,
        'department_name' => $department->department_name,
        'department_parent_id' => $department->department_parent_id,
        'filial_id' => $department->filial_id,
      ];
    };
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  public function update(Request $request, $id)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Получаем авторизованного пользователя
    $user = $request->user();
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, $method);
    // ГЛАВНЫЙ ЗАПРОС:
    $filial = Department::withoutGlobalScope($answer['moderator'])->findOrFail($id);
    // Подключение политики
    $this->authorize('update', $filial);
    if ($request->filial_database == 1) {
      $filial->city_id = $request->city_id;
      $filial->department_name = $request->filial_name;
      $filial->department_address = $request->filial_address;
      $filial->department_phone = cleanPhone($request->filial_phone);
      $filial->filial_status = 1;
      $filial->filial_id = $id;
      $filial->editor_id = $user->id;
      $filial->save();
      if ($filial) {
        return Redirect('/current_department/'.$filial->id.'/0');
      } else {
        abort(403, 'Ошибка при оюновлении филиала!');
      };
    };
    if ($request->department_database == 1) {
      $department = Department::findOrFail($id);
      $department->city_id = $request->city_id;
      $department->department_name = $request->department_name;
      $department->department_address = $request->department_address;
      // $department->department_phone = cleanPhone($request->department_phone);
      $department->department_parent_id = $request->department_parent_id;
      $department->filial_id = $request->filial_id;
      $department->editor_id = $user->id;
      $department->save();
      if ($department) {
        return Redirect('/current_department/'.$department->filial_id.'/'.$id);
      } else {
        abort(403, 'Ошибка при обновлении отдела!');
      };
    };
  }

  public function destroy(Request $request, $id)
  {
    // Получаем метод
    $method = 'delete';
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // ГЛАВНЫЙ ЗАПРОС:
    $department = Department::with('staff')->withoutGlobalScope($answer['moderator'])->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $department);
    $user = $request->user();
    if (count($department->staff) > 0) {
      abort(403, 'Филиал/отдел не пустой');
    } else {
      if ($department->filial_status == null) {
        $filial_id = $department->filial_id;
        $department_id = $department->department_parent_id;
      } else {
        $department_id = 0;
      };
      $department->editor_id = $user->id;
      $department->save();
      $department = Department::destroy($id);
      if ($department){
        if ($department_id == 0) {
          return Redirect('/departments');
        } else {
          return Redirect('/current_department/'.$filial_id.'/'.$department_id);
        };
      } else {
        abort(403, 'Ошибка при удалении филиала/отдела');
      };
    }
  }
}


