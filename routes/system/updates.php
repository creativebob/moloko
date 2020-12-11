<?php

/*
|--------------------------------------------------------------------------
| Роуты обновлений системы
|--------------------------------------------------------------------------
|
| Обновления системы
|
*/

Route::get('/', 'UpdateController@update');
Route::get('/vkusnyashka', 'UpdateController@update_vkusnyashka');
Route::get('/add_discounts_entity', 'UpdateController@addDiscountsEntity');
Route::get('/add_discounts_recalculate_notification', 'UpdateController@addDiscountsRecalculateNotification');
Route::get('/add-mailings-entities', 'UpdateController@addMailingsEntities');
Route::get('/add-cancel-charges', 'UpdateController@addCancelCharges');
Route::get('/set-checks-templates-category', 'UpdateController@setChecksTemplatesCategory');

Route::get('/add-outlets-entity', 'UpdateController@addOutletsEntity');
Route::get('/add-outlet-settings', 'UpdateController@addOutletSettings');
Route::get('/add-stock-outlet-setting', 'UpdateController@addStockOutletSetting');
Route::get('/add-loyalty-settings', 'UpdateController@addLoyaltySettings');
