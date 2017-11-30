<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;

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
   * Store a newly created resource in storage.
   * Добавляем регион в бд
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
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
      //
  }

  // Получаем сторонние данные по области (из vk)
  public function get_vk_region(Request $request)
  {
    $region = $request->region;
    $request_params = [
    'country_id' => '1',
    'q' => $region,
    'count' => '100',
    'v' => '5.69'
    ];
    $get_params = http_build_query($request_params);
    $result = (file_get_contents('https://api.vk.com/method/database.getRegions?'. $get_params));
    echo $result;
  }
}
