<?php

namespace App\Http\ViewComposers\System;

use App\Entity;

use Illuminate\View\View;

class TmcComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right('entities', false, 'index');

        // Главный запрос
        $entities = Entity::whereTmc(true)
        // ->moderatorLimit($answer)
        // ->companiesLimit($answer)
        ->get();

        return $view->with('entities', $entities);
    }

}