<?php

namespace App\Http\ViewComposers;

use App\Unit;

use Illuminate\View\View;

class UnitsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('units', false, 'index');

        // Главный запрос
        $units = Unit::moderatorLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('units_category_id', $view->units_category_id)
        ->get();

        $units_attributes = $units->mapWithKeys(function ($item) {
            return [$item->id => ['data-abbreviation' => $item->abbreviation]];
        })->all();

        return $view->with(compact('units', 'units_attributes'));
    }
}