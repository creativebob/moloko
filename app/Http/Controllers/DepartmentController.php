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

  // Получаем сторонние данные по 
  public function current_department(Request $request, $section_id, $item_id)
  {

    // Подключение политики
    $this->authorize('index', Department::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $departments = Department::with(['staff', 'staff.position', 'staff.user'])
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('moderation', 'desc')
    ->get();

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
    // Инфо о странице
    $page_info = pageInfo('departments');
    
    $data = [
      'section_name' => 'departments',
      'item_name' => 'staff',
      'section_id' => $section_id,
      'item_id' => $item_id,
    ];
    return view('departments.index', compact('departments_tree', 'positions', 'data', 'staff', 'page_info', 'departments')); 
  }

  public function create()
  {
    //
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

    // Пишем филиал
    if (isset($request->filial_db)) {
      // Если город найден, то меняем значение на 1, пишем в базу и отдаем результат
      $filial = new Department;
      $filial->company_id = $company_id;
      $filial->city_id = $request->city_id;
      $filial->department_name = $request->filial_name;
      $filial->address = $request->filial_address;
      $filial->phone = cleanPhone($request->filial_phone);
      $filial->filial_status = 1;
      $filial->author_id = $user_id;
      $filial->save();

      if($filial) {

        // $link = 'current_department/'.$filial->id.'/0';

        // Создаем папку в файловой системе
        $link_for_folder = 'public/companies/' . $company_id . '/'. $filial->id . '/users';
        Storage::makeDirectory($link_for_folder);

        $link_for_folder = 'public/companies/' . $company_id . '/'. $filial->id . '/sites';
        Storage::makeDirectory($link_for_folder);

        $link_for_folder = 'public/companies/' . $company_id . '/'. $filial->id . '/goods';
        Storage::makeDirectory($link_for_folder);

        $link_for_folder = 'public/companies/' . $company_id . '/'. $filial->id . '/documents';
        Storage::makeDirectory($link_for_folder);

        $link = 'departments';
        return redirect($link);

      } else {
        abort(403, 'Ошибка при записи филиала!');
      };
    };

    // Пишем отделы
    if (isset($request->department_db)) {
      // dd($request);
      $department = new Department;
      $department->company_id = $company_id;

      $department_name_old = $request->department_name;
      $first = mb_substr($department_name_old,0,1, 'UTF-8');//первая буква
      $last = mb_substr($department_name_old,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $department_name = $first.$last;
      $department->department_name = $department_name;
      if($request->department_address == '') {
      } else {
        $department->address = $request->department_address;
      };
      if($request->department_phone == '') {
      } else {
        $department->phone = cleanPhone($request->department_phone);
      };
      $department->filial_id = $request->filial_id;
      $department->department_parent_id = $request->department_parent_id;
      $department->author_id = $user_id;
      $department->save();
      $department_id = $department->id;
      if($department){

        $link = 'current_department/'.$request->filial_id.'/'.$department_id;
        return redirect()->route($link);

        // return Redirect('/current_department/'.$request->filial_id.'/'.$department_id);
      } else {
        abort(403, 'Ошибка при записи отдела!');
      };
    };
  }

  public function show($id)
  {
    
  }

  public function edit($id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
   $department = Department::with('city')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $department);

    if ($department->filial_status == 1) {
      // Меняем филиал

      $result = [
        'city_id' => $department->city_id,
        'city_name' => $department->city->city_name,
        'filial_name' => $department->department_name,
        'filial_address' => $department->address,
        'filial_phone' => decorPhone($department->phone),
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
      if (isset($department->address)) {
        $department_address = $department->address;
      } else {
        $department_address = '';
      };
      if (isset($department->phone)) {
        $department_phone = decorPhone($department->phone);
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

  public function update(DepartmentRequest $request, $id)
  {

    // Получаем авторизованного пользователя
    $user = $request->user();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__))
    ;
    // ГЛАВНЫЙ ЗАПРОС:
    $department = Department::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $department);

    if ($request->filial_db == 1) {
      $department->city_id = $request->city_id;
      $department->department_name = $request->filial_name;
      $department->address = $request->filial_address;
      $department->phone = cleanPhone($request->filial_phone);
      $department->filial_status = 1;
      $department->editor_id = $user->id;
      $department->save();

      if ($department) {
        return Redirect('/current_department/'.$department->id.'/0');
      } else {
        abort(403, 'Ошибка при обновлении филиала!');
      };
    };

    if ($request->department_db == 1) {

      $department->city_id = $request->city_id;
      $department_name_old = $request->department_name;
      $first = mb_substr($department_name_old,0,1, 'UTF-8');//первая буква
      $last = mb_substr($department_name_old,1);//все кроме первой буквы
      $first = mb_strtoupper($first, 'UTF-8');
      $department_name = $first.$last;
      $department->department_name = $department_name;
      $department->address = $request->department_address;
      $department->phone = cleanPhone($request->department_phone);
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

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $department = Department::with('staff')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $department);

    $user = $request->user();

    if (count($department->staff) > 0) {
      abort(403, 'Филиал/отдел не пустой');
    } else {
      $filial_status = $department->filial_status;
      if ($filial_status == null) {
        $filial_id = $department->filial_id;
        $department_parent_id = $department->department_parent_id;
        if ($department_parent_id == $filial_id) {
          $department_parent_id = 0;
        };
      } else {
        $department_parent_id = 0;
      };
      $department->editor_id = $user->id;
      $department->save();
      $department = Department::destroy($id);
      if ($department){
        if ($filial_status == 1) {
          return Redirect('/departments');
        } else {
          return Redirect('/current_department/'.$filial_id.'/'.$department_parent_id);
        };
      } else {
        abort(403, 'Ошибка при удалении филиала/отдела');
      };
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

    // Проверка отдела в нашей базе данных
    $department = Department::where([
      ['department_name', '=', $request->department_name],
      ['filial_id', '=', $request->filial_id],
    ])->first();

    if ($department) {
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