<?php

namespace App\Http\ViewComposers\Project;

use App\CatalogsGoodsItem;
use Illuminate\View\View;

class CatalogsGoodsItemsFilterComposer
{
    public function compose(View $view)
    {

        $catalogs_goods_items = $view->catalog_goods_items->pluck('name', 'id');
        return $view->with(compact('catalogs_goods_items'));
    }

}