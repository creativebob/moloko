<?php

namespace App\Http\ViewComposers;

use App\UnitsCategory;

use Illuminate\View\View;

class UnitsCategoriesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_units_categories = operator_right('units_categories', false, 'index');

        // Главный запрос
        $units_categories = UnitsCategory::moderatorLimit($answer_units_categories)
        ->systemItem($answer_units_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        return $view->with('units_categories', $units_categories);
    }

}