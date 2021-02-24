<?php

namespace App\Http\View\Composers\System;

use App\ServicesCategory;

use Illuminate\View\View;

class ServicesCategoriesTreeComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods_categories', false, 'index');

        // Главный запрос
        $servicesCategories = ServicesCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort')
        ->get([
            'id',
            'name',
            'parent_id',
            'level'
        ]);

        $servicesCategoriesTree = buildTree($servicesCategories);
//        dd($goodsCategoriesTree);

        return $view->with('servicesCategoriesTree', $servicesCategoriesTree);
    }

}
