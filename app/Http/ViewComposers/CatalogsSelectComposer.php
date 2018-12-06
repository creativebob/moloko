<?php

namespace App\Http\ViewComposers;

use App\Catalog;

use Illuminate\View\View;

class CatalogsSelectComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs', false, 'index');

        $catalogs = Catalog::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->whereSite_id(2)
        ->get(['id','name','parent_id']);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $catalogs_list = getSelectTree($catalogs, ($view->parent_id ?? null), ($view->disable ?? null), ($view->id ?? null));

        return $view->with('catalogs_list', $catalogs_list);
    }

}