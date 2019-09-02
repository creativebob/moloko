<?php

namespace App\Http\ViewComposers\Project;

use App\Article;
use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsWeightFilterComposer
{
	public function compose(View $view)
	{

        $catalog_goods_item = $view->catalog_goods_item;

        $prices_goods = PricesGoods::with([
        	'goods_public'
        ])
        ->whereHas('catalogs_item', function ($q) use($catalog_goods_item) {
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
	           'catalogs_goods_item_id',
	           'goods_id'
           ]);
//        dd($prices_goods);
		
		$articles_ids = [];
		foreach ($prices_goods as $price_goods) {
			$articles_ids[] = $price_goods->goods_public->article_id;
		}
//		dd($articles_ids);
		
		$articles = Article::
//
//			->toBase()
//		->
		find($articles_ids);
//		dd($articles);
		
//		dd($articles->first()->weight_gram);
        
        $weight['step'] = '100';
		$weight['min'] = $articles->min('weight_gram');
		$weight['max'] = $articles->max('weight_gram');
//		dd($weight);
		
        return $view->with(compact('weight'));
    }

}