<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::any('/lol', function(Request $request) {
// 	return $request;
// });


// Route::middleware('auth:api')->get('/user', function (Request $request) {
// 	return $request->user();
// });

Route::post('/goods_check', 'Api\GoodsController@checkArticle');


// -------------------------------------- Основные операции ------------------------------------------
// Сортировка
Route::post('/sort/{entity_alias}', 'Api\AppController@ajax_sort');
// Системная запись
Route::post('/system_item', 'Api\AppController@ajax_system_item');
// Отображение на сайте
Route::post('/display', 'Api\AppController@ajax_display');

// Route::group(['namespace' => 'Api'], function() {

//     // Public routes (auth not required)
// 	Route::group([], function() {
// 		Route::any('/goods_check', 'GoodsController@checkArticle');
//         // more public routes...
// 	});

//     // Private routes (auth required)
// 	Route::group(['middleware' => 'auth:api'], function() {
// 		Route::any('/goods_check2', 'GoodsController@checkArticle2');
//         // more private routes...
// 	});

// });




// Route::get('/site/boot', 'SiteController@boot');
// Route::get('/site/content', 'SiteController@content');


