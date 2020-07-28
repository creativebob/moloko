<?php

namespace App\Http\View\Composers\System;

use App\GoodsCategory;

use Illuminate\View\View;

class GoodsCategoriesTreeComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods_categories', false, 'index');

        // Главный запрос
        $goodsCategories = GoodsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort')
        ->get([
            'id',
            'name',
            'parent_id',
            'level'
        ]);

        $goodsCategoriesTree = buildTree($goodsCategories);
//        dd($goodsCategoriesTree);

        return $view->with('goodsCategoriesTree', $goodsCategoriesTree);
    }

}
