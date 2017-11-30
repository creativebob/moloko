<?php

namespace App\Http\Controllers;


use App\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CityController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      
      // dd($data);

      // $dates = Area::join('regions', 'areas.region_id', '=', 'regions.id')
    //            ->select('areas.id as area_id', 'areas.area_name', 'regions.id as region_id', 'regions.region_name')
    //            ->get();

    $dates = Region::all();
    return view('cities', compact('dates'));    

    // foreach ($dates as $data) {
    //  echo "Id: ".$data->region_id.", название области: ".$data->region_name."\r\nId района: ".$data->area_id.", название района: ".$data->area_name."\r\n";
    // }
        
        // $regions = DB::table('regions')->get();

        // foreach ($regions as $region)
        // {
        //     var_dump($region->region_name);
        //     var_dump($region->area_name);
        // }
      // return view('cities', compact('dates'));
                // echo $dates[1]->area_id;
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
      //
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

  // Получаем сторонние данные по городу и району (из vk)
  public function get_vk_city(Request $request)
  {
    $city = $request->city;
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
