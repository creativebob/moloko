<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoodsItem;

use Illuminate\View\View;

class CatalogsGoodsItemsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_goods', false, 'index');

        // Главный запрос
        $catalogs_goods_items = CatalogsGoodsItem::where('catalogs_goods_id', $view->catalog_id)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $catalogs_goods_items_select = getSelectTree($catalogs_goods_items);
        // dd($catalogs_goods_items_select);


        return $view->with(compact('catalogs_goods_items_select'));
    }

}
