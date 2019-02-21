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
// Route::get('/{city}/telegram', 'Project\IndexProjectController@telegram');

// Сбрасываем кеш
Route::get('/cache/delete/{domain}', 'Project\IndexProjectController@delete_cache');


// Первый запуск
Route::get('/', 'Project\IndexProjectController@start')->name('start');

// // База знаний (статична)
// Route::get('/knowledge/{link}', 'Project\IndexProjectController@knowledge');

// Новости
// Route::get('/news', 'Project\NewsProjectController@index')->name('news.index');


// Коллектив
// Route::get('/team', 'Project\IndexProjectController@team')->name('team.index');

// Route::any('/team/question', 'Project\IndexProjectController@question')->name('team.question');

// Контакты
Route::get('/contacts', 'Project\IndexProjectController@contacts')->name('contacts');

// Отправка
Route::post('/sending', 'Project\IndexProjectController@sending')->name('sending');
// Отправлено
Route::get('/success', 'Project\IndexProjectController@success')->name('success');
// Ошибка
Route::get('/error', 'Project\IndexProjectController@error')->name('error');

// Смена города
// Route::get('/change_city/{city_id}/{page_alias?}', 'Project\IndexProjectController@change_city')->name('change_city');

// Страницы
Route::get('/{alias}', 'Project\IndexProjectController@index')->name('index');
// Конкретная страница
// Route::get('/{city}/{alias}/{link}', 'Project\IndexProjectController@show')->name('show');

