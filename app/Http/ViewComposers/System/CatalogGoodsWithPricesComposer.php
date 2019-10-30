<?php

namespace App\Http\ViewComposers\System;

use App\CatalogsGoods;

use Illuminate\View\View;

class CatalogGoodsWithPricesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cg = operator_right('catalogs_goods', false, getmethod('index'));

        $catalog_goods = CatalogsGoods::with([
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
                            ->where('filial_id', \Auth::user()->filial_id)
                            ->where('archive', false);
                    },
                    'childs'
                ]);
            }
        ])
            ->moderatorLimit($answer_cg)
            ->companiesLimit($answer_cg)
            ->authors($answer_cg)
            ->whereHas('sites', function ($q) {
                $q->where('id', 1);
            })
            ->findOrFail($view->id);
//         dd($catalog_goods);

        return $view->with(compact('catalog_goods'));
    }

}
