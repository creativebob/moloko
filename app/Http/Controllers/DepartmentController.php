<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Department;
use App\City;
use App\Position;
use App\Staffer;
use App\Page;
use App\Right;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
// $this->authorize('index', User::class);
    $user = Auth::user();
    $others_item['user_id'] = $user->id;
    $system_item = null;
    if (isset($user->company_id)) {
      // Если у пользователя есть компания
      $departments = Department::with(['staff', 'staff.position', 'staff.user'])
              ->whereCompany_id($user->company_id)
              ->systemItem($system_item) // Фильтр по системным записям
              ->get();
      $positions_list = Position::whereCompany_id($user->company_id)
                        ->orWhereNull('company_id')->pluck('position_name', 'id');
    } else {
      // Если нет, то бог без компании
      if ($user->god == 1) {
        $departments = Department::with(['staff', 'staff.position', 'staff.user'])->get();
        $positions_list = Position::pluck('position_name', 'id');
      };
    }
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
    $departments_list = $departments->pluck('department_name', 'id');
    $page_info = Page::wherePage_alias('/departments')->whereSite_id('1')->first();
    // dd($departments_tree);
    return view('departments', compact('departments_tree', 'positions', 'positions_list', 'departments_list', 'page_info', 'pages', 'departments'));
  }

  // Получаем сторонние данные по 
  public function current_department($filial, $depart)
  {
    $user = Auth::user();
    $others_item['user_id'] = $user->id;
    $system_item = null;
    if (isset($user->company_id)) {
      // Если у пользователя есть компания
      $departments = Department::with(['staff', 'staff.position', 'staff.user'])
              ->whereCompany_id($user->company_id)
              ->systemItem($system_item) // Фильтр по системным записям
              ->get();
      $positions_list = Position::whereCompany_id($user->company_id)
                        ->orWhereNull('company_id')->pluck('position_name', 'id');
    } else {
      // Если нет, то бог без компании
      if ($user->god == 1) {
        $departments = Department::with(['staff', 'staff.position', 'staff.user'])->get();
        $positions_list = Position::pluck('position_name', 'id');
      };
    }
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
    $departments_list = $departments->pluck('department_name', 'id');
    $page_info = Page::wherePage_alias('/departments')->whereSite_id('1')->first();
    $data = [
      'filial_id' => $filial,
      'department_id' => $depart,
    ];
    return view('departments', compact('departments_tree', 'positions', 'positions_list', 'data', 'departments_list', 'staff', 'page_info', 'departments')); 
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $user = Auth::user();
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
        $filial->company_id = $user->company_id;
        $filial->city_id = $request->city_id;
        $filial->department_name = $request->filial_name;
        $filial->department_address = $request->filial_address;
        $filial->department_phone = cleanPhone($request->filial_phone);
        $filial->filial_status = 1;
        $filial->author_id = $user->id;
        $filial->save();
        if ($filial) {
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

        $department->company_id = $user->company_id;
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
        $department->author_id = $user->id;
              

        $department->save();

        $department_id = $department->id;

        if ($department) {
          return Redirect('/current_department/'.$request->filial_id.'/'.$department_id);
        } else {
          abort(403, 'Ошибка при записи отдела!');
        };
      };
    };
  }
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $department = Department::findOrFail($id);

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

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    if ($request->filial_database == 1) {
      $filial = Department::findOrFail($id);
      $filial->company_id = Auth::user()->company_id;
      $filial->city_id = $request->city_id;
      $filial->department_name = $request->filial_name;
      $filial->department_address = $request->filial_address;
      $filial->department_phone = cleanPhone($request->filial_phone);
      $filial->filial_status = 1;
      $filial->filial_id = $id;
      $filial->save();
      if ($filial) {
        return Redirect('/current_department/'.$filial->id.'/0');
      } else {
        abort(403, 'Ошибка при оюновлении филиала!');
      };
    };
    if ($request->department_database == 1) {
      $department = Department::findOrFail($id);
      $department->company_id = Auth::user()->company_id;
      $department->city_id = $request->city_id;
      $department->department_name = $request->department_name;
      $department->department_address = $request->department_address;
      // $department->department_phone = cleanPhone($request->department_phone);
      $department->department_parent_id = $request->department_parent_id;
      $department->filial_id = $request->filial_id;
      $department->save();
      if ($department) {
        return Redirect('/current_department/'.$department->filial_id.'/'.$id);
      } else {
        abort(403, 'Ошибка при обновлении отдела!');
      };
    };
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user = Auth::user();
    $department = Department::with('staff')->findOrFail($id);
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


