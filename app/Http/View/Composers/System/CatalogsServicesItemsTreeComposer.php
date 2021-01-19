<?php

namespace App\Http\View\Composers\System;

use App\CatalogsServicesItem;

use Illuminate\View\View;

class CatalogsServicesItemsTreeComposer
{

    /**
     * Разделы каталога с вложенностью
     */
    protected $catalogsGoodsItemsTree;

    /**
     * Отдаем разделы каталога на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_services_item', false, 'index');

        $catalogId = $view->catalogServices->id;

        $catalogsServicesItems = CatalogsServicesItem::where('catalogs_service_id', $catalogId)
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get();

        $this->catalogsServicesItemsTree = buildTree($catalogsServicesItems);
//        dd($this->catalogsServicesItemsTree);

        return $view->with('catalogsServicesItemsTree', $this->catalogsServicesItemsTree);
    }
}
