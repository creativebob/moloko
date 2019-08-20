<?php

namespace App\Http\ViewComposers\Project;

use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsRawsArticlesGroupsFilterComposer
{
	public function compose(View $view)
	{
		
        $catalog_goods_item = $view->catalog_goods_item;

        $prices_goods = PricesGoods::whereHas('catalogs_item', function ($q) use($catalog_goods_item) {
                $q->where([
                    'id' => $catalog_goods_item->id,
//                    'display' => true
                ]);
           } )
           ->get();
//        dd($prices_goods);
        
        $articles_groups = [];
		foreach ($prices_goods as $price_goods) {
			$article = $price_goods->goods_public->article;
			
			foreach ($article->raws as $raw) {
				foreach ($raw->metrics as $metric) {
					if ($metric->pivot->value == 2)
						$articles_groups[] = $raw->article->group;
					}
				}
			}
		
		$articles_groups = collect($articles_groups)->unique();
//		dd($articles_groups);
		
        return $view->with(compact('articles_groups'));
    }

}