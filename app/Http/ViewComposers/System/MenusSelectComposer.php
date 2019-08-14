<?php

namespace App\Http\ViewComposers\System;

use App\Menu;

use Illuminate\View\View;

class MenusSelectComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('menus', false, 'index');

        // Главный запрос
        $menus = Menu::moderatorLimit($answer)
        ->systemItem($answer)
        ->where('navigation_id', $view->navigation_id)
        ->orderBy('sort', 'asc')
        ->get(['id', 'name', 'parent_id']);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $menus_list = getSelectTree($menus, ($view->parent_id ?? null), ($view->disable ?? null), ($view->item_id ?? null));

        return $view->with('menus_list', $menus_list);
    }

}