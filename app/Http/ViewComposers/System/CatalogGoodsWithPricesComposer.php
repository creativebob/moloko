<?php

namespace App\Http\ViewComposers\System;

use App\CatalogsGoods;

use Illuminate\View\View;

class CatalogGoodsWithPricesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cg = operator_right('catalogs_goods', true, getmethod('index'));

        $сatalogs_goods = CatalogsGoods::with([
            'items' => function ($q) {
                $q->with([
                    'prices' => function ($q) {
                        $q->with([
                            'product' => function($q) {
                                $q->with([
                                    'article' => function ($q) {
                                        $q->with([
                                            'photo',
                                            'manufacturer'
                                        ])
                                            ->where('draft', false);
                                    }
                                ])
                                    ->whereHas('article', function ($q) {
                                        $q->where('draft', false);
                                    })
                                    ->where('archive', false);
                            }
                        ])
                            ->whereHas('product', function ($q) {
                                $q->where('archive', false);
                            })
                            ->where('archive', false)
                            ->select([
                                'id',
                                'archive',
                                'catalogs_goods_id',
                                'catalogs_goods_item_id',
                                'price',
                            ]);
                    },
                ])
                    ->select([
                        'id',
                        'catalogs_goods_id',
                        'name',
                        'photo_id',
                        'parent_id',
                    ]);
            },
            'prices.product.article.manufacturer'
        ])
            ->moderatorLimit($answer_cg)
            ->companiesLimit($answer_cg)
            ->authors($answer_cg)
            ->filials($answer_cg)
            ->whereHas('sites', function ($q) {
                $q->whereId(1);
            })
            ->get();
//         dd($сatalogs_goods);

        $сatalogs_goods_items = [];
        $catalogs_goods_prices = [];
        foreach ($сatalogs_goods as $сatalog_goods) {
            $сatalogs_goods_items = array_merge($сatalogs_goods_items, buildTreeArray($сatalog_goods->items));
            $catalogs_goods_prices = array_merge($catalogs_goods_prices, $сatalog_goods->prices->toArray());
        }
//        dd($catalogs_goods_prices);

        $catalogs_goods_data = [
            'catalogsGoods' => $сatalogs_goods,
            'сatalogsGoodsItems' => $сatalogs_goods_items,
            'catalogsGoodsPrices' => $catalogs_goods_prices

        ];

        return $view->with(compact('catalogs_goods_data'));
    }

}
