<?php

namespace App\Http\ViewComposers;

use App\Sector;

use Illuminate\View\View;

class SectorsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('sectors', false, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($view->sector_id);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        if (isset($view->sector_id)) {

             $sectors_list = get_select_tree($sectors, $view->sector_id, 1, null);
        }

        // dd($sectors_list);

		return $view->with('sectors_list', $sectors_list);
	}

}