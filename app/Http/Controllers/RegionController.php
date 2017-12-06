<?php

namespace App\Http\Controllers;

use App\Region;
use App\Area;
use App\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      //
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
   * Добавляем регион в бд.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $region_database = $request->region_database;
    // По умолчанию значение 0
    if ($region_database == 0) {
      // Проверка области в нашей базе данных
      $region_name = $request->region_name;
      $regions = Region::where('region_name', '=', $region_name)->first();
      if ($regions) {
        $result = [
          'error_message' => 'Область уже добавлена в нашу базу!',
          'error_status' => 1
        ];
      } else {
        $result = [
          'region_database' => 1,
          'error_status' => 0
        ];
      }
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    };
    // Если область не найдена, то меняем значение на 1, пишем в базу и отдаем результат
    if ($region_database == 1) {

      $region = new Region;

      $region->region_name = $request->region_name;
      $region->region_code = $request->region_code;
      $region->region_vk_external_id = $request->region_vk_external_id;

      $region->save();

      $region_id = $region->id;
      
      $region = [
        'region_id' => $region_id,
        'region_name' => $region->region_name,
        'region_vk_external_id' => $region->region_vk_external_id
      ];
      echo json_encode($region, JSON_UNESCAPED_UNICODE);
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
   * Удаляем регион из бд.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {

    // Проверяем содержит ли район вложенные населнные пункты
    $cities = City::where('region_id', '=', $id)->first();
    $areas = Area::where('region_id', '=', $id)->first();
    if ($cities || $areas) {
      // Если содержит, то даем сообщенеи об ошибке
      $data = [
        'status' => 0,
        'msg' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
    } else {
      // Если нет, мягко удаляем
      $region = Region::destroy($id);

      if ($region){
        $data = [
          'status'=> 1,
          'type' => 'regions',
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


  /**
   * Получаем сторонние данные по области (из vk).
   *
   */
  public function get_vk_region(Request $request)
  {
    // Log::info('Передача области: '.$request->region);
    $region = $request->region; 
    $request_params = [
    'country_id' => '1',
    'q' => $region,
    'count' => '100',
    'v' => '5.69'
    ];
    $get_params = http_build_query($request_params);
    $result = (file_get_contents('https://api.vk.com/method/database.getRegions?'. $get_params));
    // var_dump($result);
    echo $result;
    
  }
}
