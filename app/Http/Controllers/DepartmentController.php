<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Department;
use App\City;
use App\Position;
use App\Staffer;
use App\Page;
use App\Right;
// Политика
use App\Policies\DepartmentPolicy;
// Валидация
use App\Http\Requests\DepartmentRequest;
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
    ->moderatorFilter($answer['dependence'])
    ->companiesFilter($answer['company_id'])
    ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer['all_authors'])
    ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
    ->orderBy('moderated', 'desc')
    ->get();
    // Получаем список должностей
    $positions_list = Position::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer['dependence'])
    ->companiesFilter($answer['company_id'])
    ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer['all_authors'])
    ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
    ->pluck('position_name', 'id');
    // dd($departments);
    $departments_db = $departments->toArray();
    //Создаем масив где ключ массива является ID меню
    $departments_id = [];
    foreach ($departments_db as $department) {
      $departments_id[$department['id']] = $department;
    };
    //Функция построения дерева из массива от Tommy Lacroix
    
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
    $page_info = pageInfo($this->entity_name);
    // dd($departments_tree);
    return view('departments', compact('departments_tree', 'positions', 'positions_list', 'departments_list', 'page_info', 'pages', 'departments'));
  }

  // Получаем сторонние данные по 
  public function current_department(Request $request, $section_id, $item_id)
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
    ->moderatorFilter($answer['dependence'])
    ->companiesFilter($answer['company_id'])
    ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer['all_authors'])
    ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
    ->orderBy('moderated', 'desc')
    ->get();
    // Получаем список должностей
    $positions_list = Position::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer['dependence'])
    ->companiesFilter($answer['company_id'])
    ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer['all_authors'])
    ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
    ->pluck('position_name', 'id');
    // dd($departments);
    $departments_db = $departments->toArray();
    //Создаем масив где ключ массива является ID меню
    $departments_id = [];
    foreach ($departments_db as $department) {
      $departments_id[$department['id']] = $department;
    };
    //Функция построения дерева из массива от Tommy Lacroix
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

  public function store(DepartmentRequest $request)
  {
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
      // По умолчанию значение 0
      if ($request->filial_database == 0) {
        // Проверка города в нашей базе данных
        $city_name = $request->city_name;
        $cities = City::where('city_name', 'like', $city_name.'%')->get();
        $count = $cities->count();
        if ($count > 0) {
          $objRes = (object) [];
          foreach ($cities as $city) {
            $city_id = $city->id;
            $city_name = $city->city_name;
            if ($city->area_id == null) {
              $area_name = '';
              $region_name = $city->region->region_name;
            } else {
              $area_name = $city->area->area_name;
              $region_name = $city->area->region->region_name;
            };
            $objRes->city_id[] = $city_id;
            $objRes->city_name[] = $city_name;
            $objRes->area_name[] = $area_name;
            $objRes->region_name[] = $region_name;
          };
          $result = [
            'error_status' => 0,
            'cities' => $objRes,
            'count' => $count
          ];
        } else {
          $result = [
            'error_message' => 'Населенный пункт не существует в нашей базе данных, добавьте его!',
            'error_status' => 1
          ];
        };
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      };
      // Если город найден, то меняем значение на 1, пишем в базу и отдаем результат
      if ($request->filial_database == 1) {
        $filial = new Department;
        $filial->company_id = $company_id;
        $filial->city_id = $request->city_id;
        $filial->department_name = $request->filial_name;
        $filial->department_address = $request->filial_address;
        $filial->department_phone = cleanPhone($request->filial_phone);
        $filial->filial_status = 1;
        $filial->author_id = $user_id;
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
   $department = Department::withoutGlobalScope(ModerationScope::class)->findOrFail($id);
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
    $this->authorize('update', $site);
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
    // ГЛАВНЫЙ ЗАПРОС:
   $department = Department::with('staff')->withoutGlobalScope(ModerationScope::class)->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $department);
    $user = $request->user();
    if (count($department->staff) > 0) {
      abort(403, 'Филиал/отдел не пустой');
    } else {
      if ($department->filial_status == null) {
        $filial_id = $department->filial_id;
        $department_id = $department->id;
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


