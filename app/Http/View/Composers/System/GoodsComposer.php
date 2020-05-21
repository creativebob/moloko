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

        $goods_categories = GoodsCategory::with([
            'goods' => function ($q) {
                $q->with([
                    'article' => function ($q) {
                        $q->where([
                            'draft' => false,
                            'kit' => false
                        ]);
                    }
                ])
                    ->where('archive', false);
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
        ->orderBy('sort', 'asc')
        ->get();
//         dd($goods_categories);

        return $view->with(compact('goods_categories'));
    }
}
