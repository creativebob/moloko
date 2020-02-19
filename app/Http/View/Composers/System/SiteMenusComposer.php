<?php

namespace App\Http\View\Composers\System;

use App\Menu;

use Illuminate\View\View;

class SiteMenusComposer
{
	public function compose(View $view)
	{

        // Список меню для сайта
        $answer = operator_right('menus', false, 'index');

        $menus = Menu::whereNavigation_id(1) // Только для сайтов, разделы сайта
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->get(['id', 'name']);

        return $view->with('menus', $menus);

    }

}
