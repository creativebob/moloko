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

Route::get('/site', 'SiteController@show');

Route::get('/vacancies', 'StafferController@vacancies');
Route::get('/team', 'StafferController@team');
Route::get('/news', 'NewsController@news');
Route::get('/news/{link}', 'NewsController@show');

Route::get('/{alias}', 'PageController@api');
 

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
