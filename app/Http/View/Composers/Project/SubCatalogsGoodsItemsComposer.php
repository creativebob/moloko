<?php

namespace App\Http\View\Composers\Project;

use App\CatalogsGoods;
use Illuminate\View\View;

class SubCatalogsGoodsItemsComposer
{
    public function compose(View $view)
    {
        $catalogs_goods_item = $view->catalogs_goods_item;
//        dd($catalogs_goods_item);

        // TODO - 14.04.20 - Уже ближе к универсальности, но все равно пока заточено под РХ
        $parent = null;
        if ($catalogs_goods_item->level == 1) {
            $site = $view->site;

            $catalogs_goods_item->load([
                'childs_prices',
                'catalog',
            ]);

            $parent = $catalogs_goods_item;

        } else {
            $catalogs_goods_item->load([
                'parent' => function ($q) {
                    $q->with([
                        'childs',
                        'catalog'
                    ]);
                }
            ]);

            $parent = $catalogs_goods_item->parent;
        }
//        dd($parent);

        return $view->with(compact('parent'));
    }

}
