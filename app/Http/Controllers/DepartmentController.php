<?php

namespace App\Http\Controllers;

use App\Department;
use App\City;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $departments = Department::all();
      $cities = City::all();
      return view('departments', compact('departments', 'cities'));
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
      // По умолчанию значение 0
      if ($request->filial_database == 0) {
        // Проверка города в нашей базе данных
        $city_name = $request->city_name;

        $result = City::where('city_name', 'like', $city_name.'%')->first();
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

        if ($result) {
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
      // Если город не найден, то меняем значение на 1, пишем в базу и отдаем результат
      if ($request->filial_database == 1) {

          $filial = new Department;

          $filial->city_id = $request->city_id;
          $filial->company_id = 1;
          $filial->department_name = $request->filial_name;
          $filial->department_address = $request->filial_address;
          $filial->department_phone = cleanPhone($request->filial_phone);
          $filial->department_parent_id = null;
          $filial->filial_status = 1;

          $filial->save();

          $filial_id = $filial->id;
          
          $filial = [
            'filial_id' => $filial->id,
            'filial_name' => $filial->department_name,

          ];
          echo json_encode($filial, JSON_UNESCAPED_UNICODE);
      };

      // Пишем отделы
      if ($request->department_database == 2) {
        // Проверка города в нашей базе данных
        $department_name = $request->department_name;
        $department_parent_id = $request->department_parent_id;

        $result = Department::where([
          ['department_name', 'like', $department_name.'%'],
          ['department_parent_id', '=', $department_parent_id],
        ])->first();


        if ($result) {
          $result = [
            'error_status' => 1,
          ];
        } else {
          $result = [
            'error_message' => 'Такой отдел уже существует',
            'error_status' => 0
          ];
        };

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      };
      // Если город не найден, то меняем значение на 1, пишем в базу и отдаем результат
       if ($request->filial_database == 3) {

          $department = new Department;

          $department->city_id = $request->city_id;
          $department->company_id = 1;
          $department->department_name = $request->filial_name;
          $department->department_address = $request->filial_address;
          $department->department_phone = cleanPhone($request->filial_phone);
          $department->department_parent_id = null;
          $department->filial_status = 1;

          $department->save();

          $filial_id = $filial->id;
          
          $department = [
            'filial_id' => $filial->id,
            'filial_name' => $filial->department_name,

          ];
          echo json_encode($department, JSON_UNESCAPED_UNICODE);
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

    $filial = Department::whereDepartment_parent_id($id)->first();

    if ($filial) {
      // Если содержит, то даем сообщенеи об ошибке
      $data = [
        'status' => 0,
        'msg' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
    } else {
      // Если нет, мягко удаляем
      $filial = Department::destroy($id);

      if ($filial){
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
    }
}
