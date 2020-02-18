<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoods;
use App\Department;
use App\Site;

use Illuminate\View\View;

class SitesWIthFilialsAndCatalogsComposer
{
	public function compose(View $view)
	{

        // Список меню для сайта
        $answer = operator_right('sites', false, 'index');

        $sites = Site::has('domains')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            // ->systemItem($answer) // Фильтр по системным записям
            ->get();
//        dd($sites);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cg = operator_right('catalogs_goods', false, getmethod('index'));

        $catalogs_goods = CatalogsGoods::with([
            'items_public:id,catalogs_goods_id,name,photo_id,parent_id',
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
                    },
                    'filial'
                ])
                    ->whereHas('goods', function ($q) {
                        $q->where('archive', false)
                            ->whereHas('article', function ($q) {
                                $q->where('draft', false);
                            });
                    })
                    ->where([
                        'archive' => false,
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
            'filials:id,name'
        ])
            ->moderatorLimit($answer_cg)
            ->companiesLimit($answer_cg)
            ->authors($answer_cg)
//            ->whereHas('filials', function($q) {
//                $q->where('id', auth()->user()->StafferFilialId);
//            })
            ->get();
//         dd($catalogs_goods);

        $catalogs_goods_items = [];
        $catalogs_goods_prices = [];
        foreach ($catalogs_goods as $catalog_goods) {
            $catalogs_goods_items = array_merge($catalogs_goods_items, buildTreeArray($catalog_goods->items_public));
            $catalogs_goods_prices = array_merge($catalogs_goods_prices, $catalog_goods->prices->toArray());
        }
//        dd($catalogs_goods->first()->prices);
//        dd($catalogs_goods_prices);


        $answer = operator_right('departments', true, 'index');
        $filials = Department::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->whereNull('filial_id')
            ->get([
                'id',
                'name',
            ]);
//        dd($filials);

        $catalogs_goods_data = [
            'catalogsGoods' => $catalogs_goods,
            'catalogsGoodsItems' => $catalogs_goods_items,
            'catalogsGoodsPrices' => $catalogs_goods_prices,
            'filials' => $filials

        ];

        return $view->with(compact('sites', 'catalogs_goods_data'));

    }

}
