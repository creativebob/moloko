<?php

namespace App\Http\ViewComposers;

use App\Album;

use Illuminate\View\View;

class AlbumsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('albums', false, 'index');

        // Главный запрос
        $albums = Album::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->where('category_id', $view->albums_category_id)
        ->get();

        return $view->with('albums', $albums);
    }

}
