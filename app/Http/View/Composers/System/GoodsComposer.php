<?php

namespace App\Http\View\Composers\System;

use App\GoodsCategory;

use Illuminate\View\View;

class GoodsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods_categories', false, 'index');

        // $columns = [
        //     'id',
        //     'name',
        //     'parent_id'
        // ];

        $goodsCategories = GoodsCategory::with([
            'goods' => function ($q) {
                $q->where('archive', false)
                    ->whereHas('article', function ($q) {
                        $q->where([
                            'draft' => false,
                        ]);
                    })
                    ->with([
                        'article' => function ($q) {
                            $q->with([
                                'unit'
                            ])
                                ->where([
                                    'draft' => false,
                                ]);
                        },
                        'category',
                        'unit_for_composition'
                    ])
                    ->orderBy('sort');
            }
        ])
        ->whereHas('goods', function ($q) {
            $q->whereHas('article', function ($q) {
                $q->where([
                    'draft' => false,
                    'kit' => false
                ]);
            })
                ->where('archive', false);
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->orderBy('sort')
        ->get();
//         dd($goods_categories);

        $goods = [];
        foreach($goodsCategories as $goodsCategory) {
            foreach ($goodsCategory->goods as $item) {
//                $item->category = $relatedCategory;
                $goods[] = $item;
            }
        };
        $goods = collect($goods);

        return $view->with(compact('goodsCategories', 'goods'));
    }
}
