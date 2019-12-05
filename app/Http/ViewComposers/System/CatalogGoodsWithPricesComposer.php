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
            'items:id,catalogs_goods_id,name,photo_id,parent_id',
            'prices' => function ($q) {
                $q->with([
                    'goods' => function($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'photo',
                                    'manufacturer'
                                ])
                                ->where('draft', false)
                                ->select([
                                    'id',
                                    'name',
                                    'photo_id',
                                    'manufacturer_id',
                                    'draft'
                                ]);
                            }
                        ])
                            ->where('archive', false)
                            ->select([
                                'id',
                                'article_id',
                            ]);
                    }
                ])
                    ->whereHas('goods', function ($q) {
                        $q->where('archive', false)
                            ->whereHas('article', function ($q) {
                                $q->where('draft', false);
                            });
                    })
                    ->where([
                        'archive' => false,
                        'filial_id' => \Auth::user()->StafferFilialId
                    ])
                    ->select([
                        'prices_goods.id',
                        'archive',
                        'prices_goods.catalogs_goods_id',
                        'catalogs_goods_item_id',
                        'price',
                        'goods_id',
                        'filial_id'
                    ]);
            },
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
