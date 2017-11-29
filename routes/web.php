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

Route::any('user', 'UserController@show');



Route::get('/table', function () {
    return view('table');
});

Route::get('/users', function () {
    return view('users');
});


// Страница со списком населенных пунктов
Route::get('/cities', 'CitiesController@show');
Route::post('/cities', 'CitiesController@create');

// Получаем данные из vk
Route::post('/get-region', 'CitiesController@get_vk_region');

Route::any('/get-city', 'CityController@get_vk_city');

