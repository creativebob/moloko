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

Route::any('/update_cookies', 'CartController@update_cookies')->name('project.update_cookies');

Route::resource('/cart', 'CartController')
    ->only(['index', 'store'])
    ->names('project.cart');

Route::resource('/estimates', 'EstimateController')
    ->only(['index', 'show'])
    ->names('project.estimates');

Route::get('/goods-composition/{id}/', 'AppController@goods_composition')->name('project.goods_composition');

Route::post('/site_user_login', 'AppController@site_user_login')->name('project.site_user_login');
Route::get('/confirmation', 'AppController@confirmation')->name('project.confirmation');

// Генерация access_code и отправка его на телефон пользователя
Route::post('/get_access_code', 'AppController@get_access_code')->name('project.get_access_code');
Route::post('/get_sms_code', 'AppController@get_sms_code')->name('project.get_sms_code');
// Route::post('/login_by_access_code', 'AppController@login_by_access_code')->name('project.login_by_access_code');
Route::get('/telegram', 'AppController@telegram')->name('project.telegram');

Route::post('/delivery_update', 'AppController@delivery_update')->name('project.delivery_update');

Route::get('logout_siteuser', 'AppController@logout_siteuser')->name('project.logout_siteuser');

Route::get('/cabinet', 'AppController@cabinet')->name('project.cabinet')->middleware('auth_usersite');
Route::post('/update_profile', 'AppController@update_profile')->name('project.update_profile');

Route::get('/{page_alias}', 'AppController@dynamic_pages')->name('project.dynamic_pages');