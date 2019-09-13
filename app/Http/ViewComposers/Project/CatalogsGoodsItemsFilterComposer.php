<?php

namespace App\Http\ViewComposers\Project;

use App\CatalogsGoodsItem;
use Illuminate\View\View;

class CatalogsGoodsItemsFilterComposer
{
    public function compose(View $view)
    {

        $catalogs_goods = $view->catalog_goods;

        $catalogs_goods_items = CatalogsGoodsItem::whereHas('catalog', function ($q) use ($catalog) {
            $q->where('id', $catalog->id);
        })
            ->get([
            'id',
        'name',
                'catalogs_goods_id'
        ]);



        return $view->with(compact('catalogs_goods_items'));
    }

}