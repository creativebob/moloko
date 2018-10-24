<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VkController extends Controller
{
    public function market()
    {


		$request_params = array(

			// 'user_id' => $user_id,
			// 'fields' => 'bdate',
			'v' => '5.87',
			'access_token' => 'c00b07cd68ea2c334ca77eac4763c313587e4f553461b2acf32568d4cd59abf5e35f3db2f3759fefdf30e',


			'owner_id' => '-roll.house.restaurant',
			'name' => 'Новый ролл',					// название товара. Ограничение по длине считается в кодировке cp1251. 
			'description' => 'Очень вкусный',		// onописание товара. 
			'main_photo_id' => '425060558',			// идентификатор фотографии обложки товара. 
			'category_id' => '2',					// идентификатор категории товара. 
			'price' => '2000'						// положительное число, обязательный параметр


		);

		$get_params = http_build_query($request_params);

		$result = json_decode(file_get_contents('https://api.vk.com/method/market.add?'. $get_params));




		dd($result);
		// echo($result -> response[0] -> bdate);
        // return view('show_session');
    }
}
