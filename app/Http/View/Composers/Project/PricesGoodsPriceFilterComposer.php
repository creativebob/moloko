<?php

namespace App\Http\View\Composers\Project;

use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsPriceFilterComposer
{
    public function compose(View $view)
    {

        $catalog_goods_items_ids = $view->catalog_goods_items->pluck('id');

        $prices_goods = PricesGoods::whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
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

        return $view->with(compact('price'));
    }
}
