<?php

namespace App\Http\View\Composers\Project;

use App\CatalogsGoods;
use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsFilterComposer
{
    public function compose(View $view)
    {

        $catalog_goods = CatalogsGoods::with([
            'items_public',
            'prices.goods_public.article.attachments.article.group'
        ])
            ->find($view->catalogs_goods_id);
//        dd($catalog_goods);

//        $catalog_goods = $view->catalog_goods->load([
//            'items_public',
//            'prices.goods_public.article.attachments.article.group'
//        ]);
//        dd($catalog_goods);

        // Разделы каталога
        $catalogs_goods_items = $catalog_goods->items_public->pluck('name', 'id');
//        dd($catalogs_goods_items);

        $catalog_goods_items_ids = $catalog_goods->items_public->pluck('id');

        $prices_goods = $catalog_goods->prices;
//        $prices_goods = PricesGoods::with([
//            'goods_public.article.attachments.article.group'
////            'goods_public'
////            => function ($q) {
////                $q->with([
////                    'article' => function($q) {
////                        $q->with([
////                            'attachments' => function($q) {
////                                $q->with([
////                                    'article' => function ($q) {
////                                        $q->with([
////                                            'group:id,name'
////                                        ])
//////                                            ->select([
//////                                                'id',
//////                                                'articles_group_id'
//////                                            ])
////                                        ;
////                                    }
////                                ])
//////                                    ->select([
//////                                        'id',
//////                                        'attachments.article_id'
//////                                    ])
////                                ;
////                            }
////                        ])
//////                        ->select([
//////                            'id',
//////                            'weight'
//////                        ])
////                        ;
////                    }
////                ])
//////                ->select([
//////                    'id',
//////                    'goods.article_id'
//////                ])
////                ;
////            }
//        ])
//        ->whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
//        ->has('goods_public')
////        ->whereHas('goods_public', function ($q) {
////           $q->has('article');
////        })
//        ->public()
//        ->get([
//            'goods_id',
//            'price',
//            'catalogs_goods_item_id',
//        ]);

        // Фильтр цены
        $price['step'] = 100;
        $price['min'] = $prices_goods->min('price');
        $price['max'] = $prices_goods->max('price');
//        dd($price);

        // Собираем артикулы
        $articles = [];
        $articles_groups = [];
        foreach ($prices_goods as $price_goods) {
            if (isset($price_goods->goods_public->article)) {
                $article = $price_goods->goods_public->article;

                $articles[] = $article;

                if ($article->attachments->isNotEmpty()) {
                    foreach ($article->attachments as $attachment) {
                        $articles_groups[] = $attachment->article->group;
                    }
                }
            }
        }

        // Фильтр веса
        $articles = collect($articles);
        $weight['step'] = 100;
        $weight['min'] = intval(($articles->min('weight_gram')*100)/100);
        $weight['max'] = intval(($articles->max('weight_gram')*100)/100);
//        dd($weight);

        // Фильтр групп вложений
        $articles_groups = collect($articles_groups)->unique();
        $articles_groups = $articles_groups->sortBy('name');
//        dd($articles_groups);

        return $view->with(compact('catalog_goods', 'price', 'weight', 'articles_groups', 'catalogs_goods_items'));
    }
}
