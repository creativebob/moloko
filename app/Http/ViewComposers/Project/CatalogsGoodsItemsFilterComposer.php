<?php

namespace App\Http\ViewComposers\Project;

use App\CatalogsGoodsItem;
use Illuminate\View\View;

class CatalogsGoodsItemsFilterComposer
{
    public function compose(View $view)
    {

        $catalogs_goods = $view->catalog_goods;

        $catalogs_goods_items = CatalogsGoodsItem::whereHas('catalog', function ($q) use ($catalogs_goods) {
            $q->where('id', $catalogs_goods->id);
        })
            ->where('display', true)
            ->get([
            'id',
            'name',
                'catalogs_goods_id'
        ]);



        return $view->with(compact('catalogs_goods_items'));
    }

}