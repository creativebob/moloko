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
Route::get('/sectors', 'UpdateController@sectors');


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

Route::get('/add_agents_entity', 'UpdateController@addAgentsEntity');
Route::get('/add_competitors_entity', 'UpdateController@addCompetitorsEntity');

Route::get('/emails_menus', 'UpdateController@emailsMenus');

Route::get('/add_plugins_entity', 'UpdateController@addPluginsEntity');
Route::get('/add_files_entity', 'UpdateController@addFilesEntity');
Route::get('/add_photo_settings_entity', 'UpdateController@addPhotoSettingsEntity');
Route::get('/add_shifts_entity', 'UpdateController@addShiftsEntity');

Route::get('/add_change_client_discount_actions', 'UpdateController@addChangeClientDiscountActions');

Route::get('/add-events-entities', 'UpdateController@addEventsEntities');
Route::get('/add-flows-entities', 'UpdateController@addFlowsEntities');

Route::get('/add-feedbacks-to-menu', 'UpdateController@addFeedbacksToMenu');
