<?php

namespace App\Http\Controllers;

use App\Department;
use App\City;
use App\Position;
use App\Employee;
use App\Page;
use Illuminate\Http\Request;
// use Department as LavMenu;



class DepartmentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // ПОлучаем данные из таблицы в массиве
    $departments_db = Department::all()->toArray();
    //Создаем масив где ключ массива является ID меню
    $departments_id = [];
    foreach ($departments_db as $department) {
      $departments_id[$department['id']] = $department;
    };
    //Функция построения дерева из массива от Tommy Lacroix
    $departments = [];
    foreach ($departments_id as $id => &$node) {   
      //Если нет вложений
      if (!$node['department_parent_id']){
        $departments[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $departments_id[$node['department_parent_id']]['children'][$id] = &$node;
      }
    };
    $positions_list = Position::all()->pluck('position_name', 'id');
    $tree = Department::orderBy('department_parent_id')->get()->pluck('department_name', 'id');
    $employees = Employee::get();
    $positions = Position::get();
    $pages = Page::wherePage_alias('/departments')->first();
    return view('departments', compact('departments', 'positions', 'positions_list', 'tree', 'employees', 'pages'));
    // dd($positions);
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
    // Пишем филиал
    if (isset($request->filial_database)) {
      // По умолчанию значение 0
      if ($request->filial_database == 0) {
        // Проверка города в нашей базе данных
        $city_name = $request->city_name;

        $result = City::where('city_name', 'like', $city_name.'%')->first();
        if ($result) {
          $cities = City::where('city_name', 'like', $city_name.'%')->get();
          $count = City::where('city_name', 'like', $city_name.'%')->count();

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

        $filial->company_id = 1;
        $filial->city_id = $request->city_id;
        $filial->department_name = $request->filial_name;
        $filial->department_address = $request->filial_address;
        $filial->department_phone = cleanPhone($request->filial_phone);
        $filial->filial_status = 1;

        $filial->save();
        
        $filial = [
          'filial_id' => $filial->id,
          'filial_name' => $filial->department_name,
        ];
        echo json_encode($filial, JSON_UNESCAPED_UNICODE);
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

        $department->company_id = 1;
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
        
        // if($request->city_id == '') {
        //   $request->city_id = null;
        // } else {
        //   $department->city_id = $request->city_id;
        // };
        // $department->city_id = null;
        

        
        // $department->department_parent_id = $request->department_parent_id;
        // $department->filial_status = 0;
        

        $department->save();

        if ($department) {
          $filial_id = $request->filial_id;
          $department_id = $department->id;
           return Redirect('current_department/'.$filial_id.'/'.$department_id.'/0');
        } else {
          $error = 'ошибка';
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
      //
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
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    // Удаляем ajax
    // Проверяем содержит ли филиал вложения
    $department = Department::whereDepartment_parent_id($id)->first();
    if ($department) {
      // Если содержит, то даем сообщенеи об ошибке
      $data = [
        'status' => 0,
        'msg' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
    } else {
      // Если нет, мягко удаляем

      $department = Department::destroy($id);

      if ($department){
        $data = [
          'status'=> 1,
          'type' => 'departments',
          'id' => $id,
          'msg' => 'Успешно удалено'
        ];
        
      } else {
        // В случае непредвиденной ошибки
        $data = [
          'status' => 0,
          'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
        ];
      };
    };
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    // $depart =  Department::find($id);
    // return Redirect('current_department/'.$depart->filial_id.'/'.$depart->department_parent_id.'/0');
  }

  // Получаем сторонние данные по 
  public function current_department($filial, $depart, $position)
  {
    $departments_db = Department::all()->toArray();
    //Создаем масив где ключ массива является ID меню
    $departments_id = [];
    foreach ($departments_db as $department) {
      $departments_id[$department['id']] = $department;
    };
    //Функция построения дерева из массива от Tommy Lacroix
    $departments = [];
    foreach ($departments_id as $id => &$node) {   
      //Если нет вложений
      if (!$node['department_parent_id']){
        $departments[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $departments_id[$node['department_parent_id']]['children'][$id] = &$node;
      }
    };
    $data = [
      'filial_id' => $filial,
      'department_id' => $depart,
      'position_id' => $position,
    ];
    $positions_list = Position::all()->pluck('position_name', 'id');
    $tree = Department::all()->pluck('department_name', 'id');
    $employees = Employee::all();
    $positions = Position::get();
    $pages = Page::wherePage_alias('/departments')->first();
    return view('departments', compact('departments', 'positions', 'positions_list', 'data', 'tree', 'employees', 'pages')); 
  }
}


