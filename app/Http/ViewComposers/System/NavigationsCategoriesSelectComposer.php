<?php

namespace App\Http\ViewComposers\System;

use App\NavigationsCategory;

use Illuminate\View\View;

class NavigationsCategoriesSelectComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('navigations_categories', false, 'index');

        // Главный запрос
        $navigations_categories = NavigationsCategory::moderatorLimit($answer)
        // ->systemItem($answer)
        ->companiesLimit($answer)
        // ->template($answer)
        // ->whereDisplay(1)
        // ->has('albums')
        ->orderBy('sort', 'asc')
        ->get(['id','name']);
        // dd($navigations_categories);


        return $view->with('navigations_categories', $navigations_categories);
    }

}