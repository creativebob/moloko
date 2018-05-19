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

Route::get('/site', 'SiteController@api_index');

Route::get('/{city}/vacancies', 'StafferController@api_index_vacancies');
Route::get('/{city}/team', 'StafferController@api_index_team');

Route::get('/{city}/news', 'NewsController@api_index');
Route::get('/{city}/news/{link}', 'NewsController@api_show');

Route::get('/{city}/{alias}', 'PageController@api');
 
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
