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

Route::get('/catalogs-goods/{catalog_slug}/{catalog_item_slug}', 'AppController@catalog_goods')->name('project.catalog_goods');
Route::get('/catalogs-services/{catalog_slug}/{catalog_item_slug}', 'AppController@catalog_services')->name('project.catalog_services');

Route::get('/price-goods/{id}/', 'AppController@price_goods')->name('project.price_goods');
