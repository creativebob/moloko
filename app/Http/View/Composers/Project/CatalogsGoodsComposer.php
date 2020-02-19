<?php

namespace App\Http\View\Composers\Project;

use App\CatalogsGoods;
use Illuminate\View\View;

class CatalogsGoodsComposer
{
    public function compose(View $view)
    {

        $site = $view->site;

        $catalog_goods = CatalogsGoods::with([
            'items' => function ($q) {
                $q->withCount('prices_public')
                    ->where('parent_id', null)
                    ->where('display', true)
                    ->orderBy('sort');
            }
        ])
            ->whereHas('filials', function($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->where([
                'display' => true
            ])
            ->orderBy('sort')
            ->first();

//	    dd($catalog_goods);

        // ------------------ Непонятные махинации ----------------------------
//        $catalogs_cur_good = $site->catalogs_goods->first();
////        dd($catalogs_cur_good);
//
//        if (is_null($catalogs_cur_good)) {
//            $catalogs_goods_items = null;
//        } else {
//            $catalogs_goods_items = buildSidebarTree($catalogs_cur_good->items);
//        }
//        dd($catalogs_goods_items);
        // ------------------ Непонятные махинации ----------------------------

        return $view->with(compact('catalog_goods'));
    }

}
