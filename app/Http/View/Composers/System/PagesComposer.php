<?php

namespace App\Http\View\Composers\System;

use App\Page;

use Illuminate\View\View;

class PagesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('pages', false, 'index');

        // Главный запрос
        $pages = Page::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->where('site_id', $view->site_id)
            ->get();

        return $view->with(compact('pages'));
    }

}
