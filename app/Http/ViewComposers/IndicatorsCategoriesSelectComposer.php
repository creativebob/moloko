<?php

namespace App\Http\ViewComposers;

use App\IndicatorsCategory;

use Illuminate\View\View;

class IndicatorsCategoriesSelectComposer
{
	public function compose(View $view)
	{

        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right('albums_categories', false, 'index');

        // Главный запрос
        $indicators_categories = IndicatorsCategory::orderBy('sort', 'asc')
        ->get(['id','name']);
        // dd($indicators_categories);

        return $view->with('indicators_categories', $indicators_categories);
    }

}