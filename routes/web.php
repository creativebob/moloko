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
Route::resource('/companies', 'CompanyController')->middleware('auth');

Route::get('/page', 'PageController@create');

// Контроллеры для отображения населенных пунктов, районов и областей
Route::resource('/cities', 'CityController')->middleware('auth');
Route::resource('/areas', 'AreaController')->middleware('auth');
Route::resource('/regions', 'RegionController')->middleware('auth');
// Получаем области и города из vk
Route::post('/city', 'CityController@get_vk_city')->middleware('auth');
Route::post('/region', 'RegionController@get_vk_region')->middleware('auth');
// Текущий добавленный/удаленный город
Route::get('/current_city/{region}/{area}/{city}', 'CityController@current_city')->middleware('auth');
// Конец блока с населенными пунктами

// Контроллеры для отображения филиалов, отделов и должностей
Route::resource('/departments', 'DepartmentController')->middleware('auth');
// Текущий добавленный/удаленный отдел
Route::get('/current_department/{parent}/{department}/{position}', 'DepartmentController@current_department')->middleware('auth');
// Должности
Route::resource('/positions', 'PositionController')->middleware('auth');
// Контроллер свободных должностей
Route::resource('/employees', 'EmployeeController')->middleware('auth');
//Конец блока филиалов, отделов и должностей

// Контроллер отображения сайтов 
Route::resource('/sites', 'SiteController')->middleware('auth');
// Контроллер отображения страниц 
Route::resource('/pages', 'PageController')->middleware('auth');
//Конец блока сайтов и страниц
