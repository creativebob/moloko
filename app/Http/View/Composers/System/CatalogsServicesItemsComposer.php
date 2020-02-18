<?php

namespace App\Http\View\Composers\System;

use App\CatalogsServicesItem;

use Illuminate\View\View;

class CatalogsServicesItemsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_services', false, 'index');

        // Главный запрос
        $catalogs_services_items = CatalogsServicesItem::where('catalogs_service_id', $view->catalog_id)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $catalogs_services_items_select = getSelectTree($catalogs_services_items);
        // dd($catalogs_services_items_select);


        return $view->with(compact('catalogs_services_items_select'));
    }

}
