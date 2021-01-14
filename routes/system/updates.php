<?php

/*
|--------------------------------------------------------------------------
| Роуты обновлений системы
|--------------------------------------------------------------------------
|
| Обновления системы
|
*/

Route::get('/roles', 'UpdateController@roles');
Route::get('/notifications', 'UpdateController@notifications');
Route::get('/charges', 'UpdateController@charges');
Route::get('/triggers', 'UpdateController@triggers');


// Одноразовые
Route::get('/', 'UpdateController@update');
Route::get('/vkusnyashka', 'UpdateController@update_vkusnyashka');
Route::get('/add_discounts_entity', 'UpdateController@addDiscountsEntity');
Route::get('/add_discounts_recalculate_notification', 'UpdateController@addDiscountsRecalculateNotification');
Route::get('/add_mailings_entities', 'UpdateController@addMailingsEntities');
Route::get('/add_cancel_charges', 'UpdateController@addCancelCharges');
Route::get('/set_checks_templates_category', 'UpdateController@setChecksTemplatesCategory');

Route::get('/add_outlets_entity', 'UpdateController@addOutletsEntity');
Route::get('/add_outlet_settings', 'UpdateController@addOutletSettings');
Route::get('/add_stock_outlet_setting', 'UpdateController@addStockOutletSetting');
Route::get('/add_loyalty_settings', 'UpdateController@addLoyaltySettings');

Route::get('/add_workplaces_entity', 'UpdateController@addWorkplacesEntity');
Route::get('/add_labels_entity', 'UpdateController@addLabelsEntity');

Route::get('/update_agents_tables_in_migrations_table', 'UpdateController@updateAgentsTablesInMigrationsTable');
Route::get('/add_outlet_agent_setting', 'UpdateController@addOutletAgentSetting');
Route::get('/add_outlet_reserves_setting', 'UpdateController@addOutletReservesSetting');

Route::get('/add_impacts_entities', 'UpdateController@addImpactsEntities');
