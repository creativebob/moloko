<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GetCityController extends Controller
{
    public function show(Request $request, $city)
    {
		$request_params = [
		'country_id' => '1',
		'q' => $city,
		'need_all' => '0',
		'count' => '100',
		'v' => '5.69'
		];
		$get_params = http_build_query($request_params);
		$result = json_decode(file_get_contents('https://api.vk.com/method/database.getCities?'. $get_params));
		print_r($result);
    
    }
}
