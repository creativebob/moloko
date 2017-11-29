<?php

namespace App\Http\Controllers;

use App\Area;
use App\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CitiesController extends Controller
{
  // Отображаем данные с бд
	public function show()
	{

		$regions = Area::join('regions', 'areas.region_id', '=', 'regions.id')
	            ->select('areas.id as area_id', 'areas.area_name', 'regions.id as region_id', 'regions.region_name')
	            ->get();

	 //  dd($dates);


		// $areas = Region::all();
		// foreach ($areas as $area) {
		// 	echo $area->$region_name;
		// }

		dd($regions);
		
		// $regions = DB::table('regions')->get();

		// foreach ($regions as $region)
		// {
		//     var_dump($region->region_name);
		//     var_dump($region->area_name);
		// }
	  // return view('cities', compact('dates'));
	            // echo $dates[1]->area_id;
	  // foreach ($dates as $data) {
	  // 	echo $data->region_id." ".$data->region_name."\n\r".$data->area_id." ".$data->area_name."\n\r";

	  // }
	  // print_r($dates);
		// $areas = Region::find(1)->areas;
		// echo $areas;
  // 	$regions = Region::find(1);
		// $areas = Area::select(
  //           [
  //             'id',
  //             'area_name',
  //           ])
		//         ->with(['regions' => function($q)
		//         {
		//            $q->select('id', 'region_name');
		//         }])
		//         ->get();

  //   dump($areas->toArray());
		// return view('cities', compact('areas'));
  }

  // Добавляем запись в бд
  public function create(Request $request)
	{
		
		$region = new Region;

    $region->region_name = $request->region_name;
    $region->region_code = $request->region_code;
    $region->region_vk_external_id = $request->region_vk_external_id;

    $region->save();

    return redirect('/cities');
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
  // Получаем сторонние данные по городу и району (из vk)
  public function get_vk_city(Request $request)
	{
	 	$city = $request->input('city');
		$request_params = [
		'country_id' => '1',
		'q' => $city,
		'need_all' => '0',
		'count' => '100',
		'v' => '5.69'
		];
		$get_params = http_build_query($request_params);
		$result = (file_get_contents('https://api.vk.com/method/database.getCities?'. $get_params));
		echo $result;
  }
}
