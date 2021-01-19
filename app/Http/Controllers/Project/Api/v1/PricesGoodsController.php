<?php

namespace App\Http\Controllers\Project\Api\v1;

use App\Models\Project\PricesGoods;
use App\Http\Controllers\Controller;

class PricesGoodsController extends Controller
{

    /**
     * Получаем прайсы товаров выбранного раздела каталога
     *
     * @param $catalogsItemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPricesFromCatalogsGoodsItem($catalogsItemId)
    {
        $pricesGoods = PricesGoods::with([
            'goods',
            'currency',
            'catalog',
        ])
            ->display()
            ->filter()
            ->where('catalogs_goods_item_id', $catalogsItemId)
            ->oldest('sort')
            ->get();
//        dd($pricesGoods);

        return response()->json($pricesGoods);
    }
}
