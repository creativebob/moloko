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
// Route::get('/{city}/telegram', 'IndexController@telegram');

// Сбрасываем кеш
Route::get('/cache/delete/{domain}', 'IndexController@delete_cache');


// Первый запуск
Route::get('/', 'IndexController@start')->name('start');

// // База знаний (статична)
// Route::get('/knowledge/{link}', 'IndexController@knowledge');

// Новости
// Route::get('/news', 'NewsController@index')->name('news.index');

// Товары
Route::get('/goods/{catalog_item_id}', 'GoodsController@index')->name('goods');

// Коллектив
// Route::get('/team', 'IndexController@team')->name('team.index');

// Route::any('/team/question', 'IndexController@question')->name('team.question');

// Контакты
Route::get('/contacts', 'IndexController@contacts')->name('contacts');

// Отправка
Route::post('/sending', 'IndexController@sending')->name('sending');
// Отправлено
Route::get('/success', 'IndexController@success')->name('success');
// Ошибка
Route::get('/error', 'IndexController@error')->name('error');

// Смена города
// Route::get('/change_city/{city_id}/{page_alias?}', 'IndexController@change_city')->name('change_city');

// Страницы
Route::get('/{alias}', 'IndexController@index')->name('index');
// Конкретная страница
// Route::get('/{city}/{alias}/{link}', 'IndexController@show')->name('show');

