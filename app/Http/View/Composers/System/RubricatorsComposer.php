<?php

namespace App\Http\View\Composers\System;

use App\Rubricator;

use Illuminate\View\View;

class RubricatorsComposer
{
	public function compose(View $view)
	{
		// Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('rubricators', false, 'index');

        // Главный запрос
        $rubricators = Rubricator::with('items')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->get();

        return $view->with(compact('rubricators'));
	}
}
