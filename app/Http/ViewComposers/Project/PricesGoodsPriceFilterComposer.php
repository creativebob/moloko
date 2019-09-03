<?php

namespace App\Http\ViewComposers\Project;

use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsPriceFilterComposer
{
    public function compose(View $view)
    {

        $catalog_goods_item = $view->catalog_goods_item;

        $prices_goods = PricesGoods::whereHas('catalogs_item', function ($q) use($catalog_goods_item) {
            $q->where([
                'id' => $catalog_goods_item->id,
//                    'display' => true
            ]);
        })
            ->has('goods_public')
            ->where([
                'display' => true,
                'archive' => false
            ])
            ->get([
                'price',
                'catalogs_goods_item_id'
            ]);

        $price['step'] = 100;
        $price['min'] = $prices_goods->min('price');
        $price['max'] = $prices_goods->max('price');
//		dd($price);

        return $view->with(compact('price'));
    }

}