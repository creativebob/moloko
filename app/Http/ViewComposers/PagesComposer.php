<?php

namespace App\Http\ViewComposers;

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
        ->where('site_id', $view->site_id)
        ->get();

        return $view->with('pages', $pages);
    }

}