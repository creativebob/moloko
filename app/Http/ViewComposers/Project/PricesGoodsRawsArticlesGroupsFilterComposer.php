<?php

namespace App\Http\ViewComposers\Project;

use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsRawsArticlesGroupsFilterComposer
{
    public function compose(View $view)
    {

//        $catalog_goods_item = $view->catalog_goods_item;
//
//        $prices_goods = PricesGoods::with([
//            'goods_public.article.raws.metrics'
//        ])
//        ->whereHas('catalogs_item', function ($q) use($catalog_goods_item) {
//            $q->where([
//                'id' => $catalog_goods_item->id,
//                'display' => true
//            ]);
//        } )
//            ->has('goods_public')
//            ->where([
//                'display' => true,
//                'archive' => false
//            ])
//            ->get();
	    $catalog_goods_items_ids = $view->catalog_goods_items->pluck('id');
	
	    $prices_goods = PricesGoods::whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
		    ->has('goods_public')
		    ->where([
			    'display' => true,
			    'archive' => false
		    ])
		    ->get();
//        dd($prices_goods);

        $articles_groups = [];
        foreach ($prices_goods as $price_goods) {
            $article = $price_goods->goods_public->article;
			
            if ($article->attachments->isNotEmpty()) {
	            foreach ($article->attachments as $attachment) {
	            	$articles_groups[] = $attachment->article->group;
	            }
            }
        }

        $articles_groups = collect($articles_groups)->unique();
//		dd($articles_groups);

        return $view->with(compact('articles_groups'));
    }

}

    // МОЖЕТ ТАК? =====================================================================================
    // public function compose(View $view)
    // {
    //     $catalog_goods_items_ids = $view->catalog_goods_items->pluck('id');

    //     $prices_goods = PricesGoods::whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
    //         ->has('goods_public')
    //         ->where([
    //             'display' => true,
    //             'archive' => false
    //         ])
    //         ->get();

    //     $articles_groups = [];
    //     foreach ($prices_goods as $price_goods) {
    //         $article = $price_goods->goods_public->article;

    //         foreach ($article->raws as $raw) {
    //             foreach ($raw->metrics as $metric) {
    //                 if ($metric->pivot->value == 2)
    //                     $articles_groups[] = $raw->article->group;
    //             }
    //         }
    //     }

    //     $articles_groups = collect($articles_groups)->unique();

    //     return $view->with(compact('articles_groups'));
    // }