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

// Route::resource('/news', 'Project\NewsProjectController')->middleware('auth');

// Route::resource('/lolkek', 'Project\ServicesProjectController')->middleware('auth');

// Кабинет
Route::resource('/', 'Project\CabinetProjectController');
Route::resource('/cabinet', 'Project\CabinetProjectController');

// Клиника
Route::resource('/clinic', 'Project\ClinicProjectController');

// Услуги
Route::get('/services/{id?}', 'Project\ServicesProjectController@show');
// Route::resource('/services', 'Project\ServicesProjectController');

// О нас
Route::resource('/about', 'Project\AboutProjectController');

// Контакты
Route::resource('/contacts', 'Project\ContactsProjectController');
