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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::group(['prefix' => '/v1',
    'namespace' => 'Api\v1',
    'as' => 'api.'
], function () {
    Route::get('/cities_list', 'CityController@cities_list');
    Route::resource('cities', 'CityController');

    Route::post('dropzone', 'PhotoController@store');

	Route::get('/categories_select/{entity}', 'AppController@categories_select');
    Route::get('/categories/{category_entity}', 'AppController@categories_index');


    Route::post('/companies/search-by-name', 'CompanyController@searchByName');



    Route::group(['prefix' => '/search'], function () {
        Route::get('/clients/{search}', 'ClientController@search');
        Route::get('/articles_groups/{search}', 'ArticlesGroupController@search');

    });


//    Route::apiResource('prices_goods', 'PricesGoodsController');
});

// Route::any('/lol', function(Request $request) {
// 	return $request;
// });


// Route::middleware('auth:api')->get('/user', function (Request $request) {
// 	return $request->user();
// });

// Прием лида
Route::any('/lead_store', 'Api\AppController@lead_store');

//Route::post('/goods_check', 'Api\GoodsController@checkArticle');


// -------------------------------------- Основные операции ------------------------------------------
// Сортировка
Route::post('/sort/{entity_alias}', 'Api\AppController@ajax_sort');
// Системная запись
Route::post('/system', 'Api\AppController@ajax_system');
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


