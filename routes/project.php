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
Route::get('/', 'AppController@start')
    ->name('project.start');


// Смена города
Route::get('/change_city/{alias}', 'AppController@change_city')
    ->name('project.change_city');


// Товары
Route::get('/catalogs-goods/{catalog_slug}/{slug}', 'CatalogsGoodsItemController@show')
    ->where('slug', '.*')
    ->name('project.catalogs_goods_items.show');

Route::get('/catalogs-goods/{catalog_slug}', 'CatalogsGoodsController@show')
    ->name('project.catalogs_goods.show');

Route::get('/prices-goods/search/{text}', 'PricesGoodsController@search')
    ->name('project.prices_goods.search');
Route::resource('/prices-goods', 'PricesGoodsController')
    ->only([
        'index',
        'show'
    ])
    ->names('project.prices_goods');


// Услуги
Route::get('/catalogs-services/{catalog_slug}/{slug}', 'CatalogsServicesItemController@show')
    ->where('slug', '.*')
    ->name('project.catalogs_services_items.show');

Route::get('/catalogs-services/{catalog_slug}', 'CatalogsServiceController@show')
    ->name('project.catalogs_services.show');

Route::get('/prices-services/search/{text}', 'PricesServiceController@search')
    ->name('project.prices_services.search');
Route::resource('/prices-services', 'PricesServiceController')
    ->only([
        'index',
        'show'
    ])
    ->names('project.prices_services');


// Остальные
Route::any('/update_cookies', 'CartController@update_cookies')
    ->name('project.update_cookies');
Route::get('/check_prices', 'CartController@check_prices')
    ->name('project.check_prices');

Route::resource('/cart', 'CartController')
    ->only([
        'index',
        'store'
    ])
    ->names('project.cart');

Route::resource('/estimates', 'EstimateController')
    ->only([
        'index',
        'show'
    ])
    ->names('project.estimates');

Route::resource('/promotions', 'PromotionController')
    ->only([
        'index',
        'show'
    ])
    ->names('project.promotions');

Route::get('/goods-composition/{id}/', 'AppController@goods_composition')->name('project.goods_composition');

Route::post('/site_user_login', 'AppController@site_user_login')->name('project.site_user_login');
Route::get('/confirmation', 'AppController@confirmation')->name('project.confirmation');
Route::post('/success', 'AppController@success')->name('project.success');


// Генерация access_code и отправка его на телефон пользователя
Route::post('/get_access_code', 'AppController@get_access_code')->name('project.get_access_code');
Route::post('/get_sms_code', 'AppController@get_sms_code')->name('project.get_sms_code');
// Route::post('/login_by_access_code', 'AppController@login_by_access_code')->name('project.login_by_access_code');
Route::get('/telegram', 'AppController@telegram')->name('project.telegram');

Route::post('/delivery_update', 'AppController@delivery_update')->name('project.delivery_update');

Route::post('logout_siteuser', 'AppController@logout_siteuser')
    ->name('project.logout_siteuser');

Route::get('/cabinet', 'AppController@cabinet')->name('project.cabinet')->middleware('auth_usersite');
Route::post('/update_profile', 'AppController@update_profile')->name('project.update_profile');

Route::get('/{page_alias}', 'AppController@dynamic_pages')->name('project.dynamic_pages');


// Оборудование
Route::resource('/tools', 'ToolController')
    ->only([
        'show'
    ])
    ->names('project.tools');

Route::resource('/forms', 'FormController')
    ->only([
        'store',
    ])
    ->names('project.forms');
