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

Route::get('/unsubscribe/{id}', 'AppController@unsubscribe')
    ->name('project.unsubscribe');


// Смена города
//Route::get('/filials', 'AppController@filials')
//    ->name('project.filials');
Route::get('/change_filial/{domain}', 'AppController@changeFilial')
    ->name('project.changeFilial');

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

Route::post('/order', 'CartController@order')
    ->name('project.order');

Route::resource('/orders', 'OrderController')
    ->only([
        'index',
    ])
    ->names('project.orders');

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

Route::get('/goods-composition/{id}/', 'AppController@goods_composition')
    ->name('project.goods_composition');

Route::post('/site_user_login', 'AppController@site_user_login')
    ->name('project.site_user_login');
Route::get('/confirmation', 'AppController@confirmation')
    ->name('project.confirmation');
Route::post('/success', 'AppController@success')
    ->name('project.success');
Route::post('/subscribed', 'AppController@subscribed')
    ->name('project.subscribed');


// Генерация access_code и отправка его на телефон пользователя
Route::post('/get_access_code', 'AppController@get_access_code')
    ->name('project.get_access_code');
Route::post('/get_sms_code', 'AppController@get_sms_code')
    ->name('project.get_sms_code');
// Route::post('/login_by_access_code', 'AppController@login_by_access_code')->name('project.login_by_access_code');
Route::get('/telegram', 'AppController@telegram')
    ->name('project.telegram');

Route::post('/shipment_update', 'AppController@shipment_update')
    ->name('project.shipment_update');


// ------------------- Профиль ---------------------------
Route::get('/profile', 'UserController@edit')
    ->name('project.user.edit');
Route::post('/user/update', 'UserController@update')
    ->name('project.user.update');
Route::post('/user/logout', 'UserController@logout')
    ->name('project.user.logout');


// ---------------------- Лайки ------------------------------
Route::resource('/likes_prices_goods', 'LikesPricesGoodsController')
    ->only([
        'store',
        'destroy'
    ]);


// --------------------- Избранное -----------------------------
Route::resource('/favorites_goods', 'FavoritesGoodsController')
    ->only([
        'index',
        'store',
        'destroy'
    ])
    ->names('project.favorites_goods');

// Оборудование
//Route::resource('/tools', 'ToolController')
//    ->only([
//        'show'
//    ])
//    ->names('project.tools');

// TODO - 29.01.21 - Костыль для переименования tools в equipment
Route::get('/equipments/{slug}', 'ToolController@show')
//    ->only([
//        'show'
//    ])
    ->name('project.equipments.show');

Route::resource('/forms', 'FormController')
    ->only([
        'store',
    ])
    ->names('project.forms');
Route::post('/forms/subscribe', 'FormController@subscribe')
    ->name('project.forms.subscribe');

// TODO - 30.04.21 - Костыль для переименования services_flows в tours
Route::get('/tours/{slug}', 'ServicesFlowController@show')
    ->name('project.tours.show');

Route::get('/{page_alias}', 'AppController@dynamic_pages')
    ->name('project.dynamic_pages');
