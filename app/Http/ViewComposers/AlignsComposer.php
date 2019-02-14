<?php

namespace App\Http\ViewComposers;

use App\Align;

use Illuminate\View\View;

class AlignsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('aligns', false, 'index');

        // Главный запрос
        $aligns = Align::get();

        return $view->with('aligns', $aligns);
    }

}