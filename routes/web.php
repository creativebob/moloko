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


Route::get('/user', 'UserController@show');
Route::post('/user', 'UserController@create');

Route::get('users', function () {
    return view('users');
});

Route::get('table', function () {
    return view('table');
});

Route::get('/cities', function () {
    return view('cities');
});


Route::post('get-city/{city}', 'GetCityController@show');

