<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('layouts.enter');
});

Auth::routes();


Route::resource('/users', 'UserController')->middleware('auth');

Route::get('/page', 'PageController@create');

// Контроллер для отображения населенных пунктов и областей
Route::resources([
  '/cities' => 'CityController',
  '/areas' => 'AreaController',
  '/regions' => 'RegionController'
]);

// Получаем области и города из vk
Route::post('/city', 'CityController@get_vk_city');
Route::post('/region', 'RegionController@get_vk_region');

Route::get('/current_city/{region}/{area}/{city}', 'CityController@current_city')->name('current_city');

// Конец блока с населенными пунктами
