<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoodsItem;

use Illuminate\View\View;

class CatalogsGoodsItemsTreeComposer
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
        $answer = operator_right('catalogs_goods_item', false, 'index');

        $catalogId = $view->catalogGoods->id;

        $catalogsGoodsItems = CatalogsGoodsItem::where('catalogs_goods_id', $catalogId)
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get();

        $this->catalogsGoodsItemsTree = buildTree($catalogsGoodsItems);
//        dd($this->catalogsGoodsItemsTree);

        return $view->with('catalogsGoodsItemsTree', $this->catalogsGoodsItemsTree);
    }
}
