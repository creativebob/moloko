<?php

namespace App\Http\View\Composers\Project;

use App\CatalogsGoodsItem;
use Illuminate\View\View;

class CatalogsGoodsItemsFilterComposer
{
    public function compose(View $view)
    {
        $catalog_goods = $view->catalog_goods->load([
            'items_public'
        ]);
        $catalogs_goods_items = $catalog_goods->items_public->pluck('name', 'id');

        return $view->with(compact('catalogs_goods_items'));
    }

}
