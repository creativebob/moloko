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

Route::get('/catalogs-goods/{all}', 'CatalogsGoodsController@show')
    ->where('all', '.*')
    ->name('project.catalogs_goods.show');
//Route::get('/catalogs-goods/{catalog_slug}/{catalog_item_slug}', 'AppController@catalogs_goods')->name('project.catalogs_goods');
//Route::get('/catalogs-services/{catalog_slug}/{catalog_item_slug}', 'AppController@catalogs_services')->name('project.catalogs_services');

Route::resource('/prices-goods', 'PricesGoodsController')
    ->only(['show'])
    ->names('project.prices_goods');

Route::resource('/estimates', 'EstimateController')
    ->only(['index', 'show'])
    ->names('project.estimates');

Route::any('/update_cookies', 'CartController@update_cookies')->name('project.update_cookies');
Route::resource('/cart', 'CartController')
    ->only(['index', 'store', 'update_cookies'])
    ->names('project.carts');
//Route::post('/cart_store', 'AppController@cart_store')->name('project.cart_store');

Route::get('/telegram', 'AppController@telegram')->name('project.telegram');

Route::post('/delivery_update', 'AppController@delivery_update')->name('project.delivery_update');