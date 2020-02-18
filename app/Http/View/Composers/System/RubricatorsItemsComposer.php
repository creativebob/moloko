<?php

namespace App\Http\View\Composers\System;

use App\RubricatorsItem;

use Illuminate\View\View;

class RubricatorsItemsComposer
{
	public function compose(View $view)
	{
		// Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('rubricators_items', false, 'index');

        // Главный запрос
        $rubricators_items = RubricatorsItem::where('rubricator_id', $view->rubricator_id)
        ->get();


        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $rubricators_items_select = getSelectTree($rubricators_items, $view->rubricators_item_id);
        // dd($rubricators_items_select);

        return $view->with(compact('rubricators_items_select'));
	}
}
