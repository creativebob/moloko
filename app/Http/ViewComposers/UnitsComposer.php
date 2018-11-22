<?php

namespace App\Http\ViewComposers;

use App\Units;

use Illuminate\View\View;

class UnitsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('units', false, 'index');

        // Главный запрос
        $units_categories = UnitsCategory::moderatorLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        return $view->with('units_categories', $units_categories);
    }

}