<?php

namespace App\Http\View\Composers\System;

use App\GoodsCategory;

use Illuminate\View\View;

class RelatedComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods_categories', false, 'index');

        $id = $view->id ?? null;
        // $columns = [
        //     'id',
        //     'name',
        //     'parent_id'
        // ];

        $relatedCategories = GoodsCategory::with([
            'goods' => function ($q) use ($id) {
                $q->where('archive', false)
                    ->when($id, function ($q) use ($id) {
                        $q->where('id', '!=', $id);
                    })
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
                        'category'
                ]);
            }
        ])
        ->whereHas('goods', function ($q) {
            $q->where('archive', false)
                ->whereHas('article', function ($q) {
                    $q->where([
                        'draft' => false,
                    ]);
            });
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->orderBy('sort')
        ->get();
//         dd($relatedCategories);

        $relatedGoods = [];
        foreach($relatedCategories as $relatedCategory) {
            foreach ($relatedCategory->goods as $item) {
//                $item->category = $relatedCategory;
                $relatedGoods[] = $item;
            }
        };

        return $view->with(compact('relatedCategories', 'relatedGoods'));
    }
}
