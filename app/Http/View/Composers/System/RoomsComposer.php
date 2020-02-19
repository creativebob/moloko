<?php

namespace App\Http\View\Composers\System;

use App\Room;

use Illuminate\View\View;

class RoomsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('rooms', false, 'index');

        // Главный запрос
        $rooms = Room::with([
            'article'
        ])
        ->where('archive', false)
        ->whereHas('article', function ($q) {
            $q->where('draft', false);
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        return $view->with(compact('rooms'));
    }

}
