<?php

/*
|--------------------------------------------------------------------------
| Роуты парсеров системы
|--------------------------------------------------------------------------
|
| Парсеры системы
|
*/

Route::get('/update_parser', 'ParserController@parser');
Route::get('/roll_house_parser', 'ParserController@roll_house_parser');
Route::get('/parser_rh_goods_metrics', 'ParserController@parserRhGoodsMetrics');
Route::get('/130420', 'ParserController@parser_130420');
Route::get('/archive_goods', 'ParserController@parserArchiveGoods');
Route::get('/prices_goods_total', 'ParserController@parserPricesGoodsTotal');
Route::get('/add_role', 'ParserController@addRole');
Route::get('/set-morphs-aliases', 'ParserController@setMorphsAliases');
Route::get('/set-organizations', 'ParserController@setOrganizations');

Route::get('/update-productions-entities-models', 'ParserController@updateProductionsEntitiesModel');
Route::get('/set-registered-at', 'ParserController@setRegisteredAt');
Route::get('/set-receipted-at', 'ParserController@setReceiptedAt');
Route::get('/set-produced-at', 'ParserController@setProducedAt');

Route::get('/start-registering-documents-command', 'ParserController@startRegisteringDocumentsCommand');

Route::get('/update-payments', 'ParserController@updatePayments');
Route::get('/set-sended-at', 'ParserController@setSendedAt');
Route::get('/create-subscribers-from-users', 'ParserController@createSubscribersFromUsers');
Route::get('/set-storage-for-consignments-items', 'ParserController@setStorageForConsignmentsItems');
Route::get('/set-storage-for-productions-items', 'ParserController@setStorageForProductionsItems');
Route::get('/set-documents-items-entities', 'ParserController@setDocumentsItemsEntities');

Route::get('/set-storage-for-reserves', 'ParserController@setStorageForReserves');
Route::get('/re-reserving', 'ParserController@reReserving');
Route::get('/set-discounts-for-estimates', 'ParserController@setDiscountsForEstimates');

Route::get('/update-payments-sign', 'ParserController@updatePaymentsSign');
Route::get('/update-clients-filial', 'ParserController@updateClientsFilial');

Route::get('/update-leads-outlet-id', 'ParserController@updateLeadsOutletId');

Route::get('/test', 'ParserController@test');
