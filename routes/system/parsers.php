<?php

/*
|--------------------------------------------------------------------------
| Роуты парсеров системы
|--------------------------------------------------------------------------
|
| Парсеры системы
|
*/

Route::get('/set_rh_cancel_grounds', 'ParserController@setRhCancelGrounds');

Route::get('/update_parser', 'ParserController@parser');
Route::get('/roll_house_parser', 'ParserController@roll_house_parser');
Route::get('/parser_rh_goods_metrics', 'ParserController@parserRhGoodsMetrics');
Route::get('/130420', 'ParserController@parser_130420');
Route::get('/archive_goods', 'ParserController@parserArchiveGoods');
Route::get('/prices_goods_total', 'ParserController@parserPricesGoodsTotal');
Route::get('/add_role', 'ParserController@addRole');
Route::get('/set_morphs_aliases', 'ParserController@setMorphsAliases');
Route::get('/set_organizations', 'ParserController@setOrganizations');

Route::get('/update_productions_entities_models', 'ParserController@updateProductionsEntitiesModel');
Route::get('/set_registered_at', 'ParserController@setRegisteredAt');
Route::get('/set_receipted_at', 'ParserController@setReceiptedAt');
Route::get('/set_produced_at', 'ParserController@setProducedAt');

Route::get('/start_registering_documents_command', 'ParserController@startRegisteringDocumentsCommand');

Route::get('/update_payments', 'ParserController@updatePayments');
Route::get('/set_sended_at', 'ParserController@setSendedAt');
Route::get('/create_subscribers_from_users', 'ParserController@createSubscribersFromUsers');
Route::get('/set_storage_for_consignments_items', 'ParserController@setStorageForConsignmentsItems');
Route::get('/set_storage_for_productions_items', 'ParserController@setStorageForProductionsItems');
Route::get('/set_documents_items_entities', 'ParserController@setDocumentsItemsEntities');

Route::get('/set_storage_for_reserves', 'ParserController@setStorageForReserves');
Route::get('/re_reserving', 'ParserController@reReserving');
Route::get('/set_discounts_for_estimates', 'ParserController@setDiscountsForEstimates');

Route::get('/update_payments_sign', 'ParserController@updatePaymentsSign');
Route::get('/update_clients_filial', 'ParserController@updateClientsFilial');

Route::get('/update_leads_outlet_id', 'ParserController@updateLeadsOutletId');

Route::get('/test', 'ParserController@test');

Route::get('/set_articles_slug', 'ParserController@setArticlesSlug');
Route::get('/set_processes_slug', 'ParserController@setProcessesSlug');

Route::get('/set_entities_types', 'ParserController@setEntitiesTypes');

Route::get('/set_seos', 'ParserController@setSeos');
