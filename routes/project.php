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

// Первый запуск
Route::get('/', 'AppController@start')->name('project.start');

Route::get('/catalogs-goods/{catalog_slug}/{catalog_item_slug}', 'AppController@catalogs_goods')->name('project.catalogs_goods');
Route::get('/catalogs-services/{catalog_slug}/{catalog_item_slug}', 'AppController@catalogs_services')->name('project.catalogs_services');

Route::get('/prices-goods/{id}/', 'AppController@prices_goods')->name('project.prices_goods');
