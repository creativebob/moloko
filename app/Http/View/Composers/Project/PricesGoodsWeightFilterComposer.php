<?php

namespace App\Http\View\Composers\Project;

use App\Article;
use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsWeightFilterComposer
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
                'catalogs_goods_item_id',
                'goods_id'
            ]);

        $articles_ids = [];
        foreach ($prices_goods as $price_goods) {
            $articles_ids[] = $price_goods->goods_public->article_id;
        }

        $articles = Article::find($articles_ids);

        $weight['step'] = '100';
        $weight['min'] = intval(($articles->min('weight_gram')*100)/100);
        $weight['max'] = intval(($articles->max('weight_gram')*100)/100);

        return $view->with(compact('weight'));
    }

}
