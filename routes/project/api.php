<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => '/v1',
    'namespace' => 'Api\v1',
    'as' => 'project.api.'
], function () {
    Route::post('/catalogs_goods_items', 'CatalogsGoodsItemController@show');

    Route::apiResource('/display_modes', 'DisplayModesController')
        ->only([
            'index'
        ]);

    Route::get('/get_prices_from_catalogs_goods_item/{catalogsItemId}', 'PricesGoodsController@getPricesFromCatalogsGoodsItem');
});

