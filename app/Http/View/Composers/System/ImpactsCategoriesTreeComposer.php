<?php

namespace App\Http\View\Composers\System;

use App\ImpactsCategory;
use Illuminate\View\View;

class ImpactsCategoriesTreeComposer
{

    /**
     * Категории
     */
    protected $impactsCategoriesTree;

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('impacts_categories', false, 'index');

        $impactsCategories = ImpactsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get();

        $this->impactsCategoriesTree = buildTree($impactsCategories);
//        dd($this->impactsCategoriesTree);

        return $view->with('impactsCategoriesTree', $this->impactsCategoriesTree);
    }
}
