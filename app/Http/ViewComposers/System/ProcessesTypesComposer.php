<?php

namespace App\Http\ViewComposers\System;

use App\ProcessesType;

use Illuminate\View\View;

class ProcessesTypesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right('processes_types', false, 'index');

        // Главный запрос
        $processes_types = ProcessesType::get();

        return $view->with(compact('processes_types'));
    }

}