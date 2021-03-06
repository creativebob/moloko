<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoods;
use Illuminate\View\View;

class CatalogGoodsWithPricesComposer
{
	public function compose(View $view)
	{
	    $settings = $view->settings;

	    $catalogsIds = $view->catalogsIds;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cg = operator_right('catalogs_goods', true, getmethod('index'));

        $catalogs_goods = CatalogsGoods::with([
            'items:id,catalogs_goods_id,name,photo_id,parent_id',
            'prices' => function ($q) use ($settings) {
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
                                    'draft',
                                    'cost_default'
                                ]);
                            },
                            'stocks'
                        ])
                        ->where('archive', false)
                        ->select([
                            'id',
                            'article_id',
                            'serial'
                        ]);
                    },
                    'currency',
                    'discounts_actual',
                    'catalogs_item.discounts_actual'
                ])
                ->whereHas('goods', function ($q) use ($settings) {
                    $q
//                        ->when($settings->firstWhere('alias', 'sale-for-order'), function ($q) {
//                        $q->where('is_ordered', true);
//                    })
//                    ->when($settings->firstWhere('alias', 'sale-for-production'), function ($q) {
//                        $q->where('is_produced', true);
//                    })
//                    ->when($settings->firstWhere('alias', 'sale-from-stock'), function ($q) {
//                        $q->whereHas('stocks', function ($q) {
//                            $q->where('filial_id', auth()->user()->StafferFilialId)
//                            ->where('free', '>', 0);
//                        });
//                    })
                    ->where('archive', false)
                    ->whereHas('article', function ($q) {
                        $q->where('draft', false);
                    });
                })
                ->where([
                    'archive' => false,
                    'filial_id' => auth()->user()->StafferFilialId
                ])
//                ->select([
//                    'prices_goods.id',
//                    'archive',
//                    'prices_goods.catalogs_goods_id',
//                    'catalogs_goods_item_id',
//                    'price',
//                    'goods_id',
//                    'filial_id'
//                ])
                ;
            },
        ])
        ->moderatorLimit($answer_cg)
        ->companiesLimit($answer_cg)
        ->authors($answer_cg)
        ->filials($answer_cg)
//        ->whereHas('filials', function ($q) {
//            $q->where('id', auth()->user()->stafferFilialId);
//        })
                ->whereIn('id', $catalogsIds)
        ->get();
//         dd($сatalogs_goods);

        $catalogs_goods_items = [];
        $catalogs_goods_prices = [];
        foreach ($catalogs_goods as $catalog_goods) {
            $catalogs_goods_items = array_merge($catalogs_goods_items, buildTreeArray($catalog_goods->items));

            $catalogs_goods_prices = array_merge($catalogs_goods_prices, $catalog_goods->prices->setAppends([
                'totalWithDiscounts',
            ])->toArray());
        }
//        dd($catalogs_goods_prices);

        $catalogs_goods_data = [
            'catalogsGoods' => $catalogs_goods,
            'catalogsGoodsItems' => $catalogs_goods_items,
            'catalogsGoodsPrices' => $catalogs_goods_prices

        ];

        return $view->with(compact('catalogs_goods_data'));
    }

}
