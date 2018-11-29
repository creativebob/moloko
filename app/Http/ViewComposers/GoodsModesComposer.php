<?php

namespace App\Http\ViewComposers;

use App\GoodsMode;

use Illuminate\View\View;

class GoodsModesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods_modes', false, 'index');

        // Главный запрос
        $goods_modes = GoodsMode::moderatorLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id', 'name']);

        // dd($view->sector_id);

        // $sectors_tree = buildTree($sectors);

        // $sectors = [];
        // foreach ($sectors_tree as $sector) {

        //     dd($sector->childrens->toArray());
        //     $sectors[] = [$sector->name => [$sector->toArray()->childrens]];
        // }

        // dd($sectors);
        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        // if (isset($view->parent_id)) {

        //     $sectors_list = get_select_tree($sectors, $view->parent_id, 1, null);
        // }

        // dd($sectors_list);

		return $view->with('goods_modes', $goods_modes);
	}

}