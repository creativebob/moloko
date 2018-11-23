<?php

namespace App\Http\ViewComposers;

use App\Sector;

use Illuminate\View\View;

class SectorsSelectComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('sectors', false, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer) // Выводим шаблоны в список
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->pluck('name', 'id');

        // ->keyBy('id')
        // ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        // $sectors_list = get_select_tree($sectors, null, 1, null);
        // dd($sectors_list);

		return $view->with('sectors_list', $sectors);
	}

}