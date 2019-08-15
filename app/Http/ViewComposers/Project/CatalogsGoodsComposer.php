<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class CatalogsGoodsComposer
{
	public function compose(View $view)
	{

	    $site = $view->site->load(['catalogs_goods' => function ($q) {
            $q->with([
                'items'
            ])
                ->where([
                    'display' => 1
                ])
                ->orderBy('sort');
        }]);

        $catalogs_cur_good = $site->catalogs_goods->first();
//        dd($catalogs_service);

        if (is_null($catalogs_cur_good)) {
            $catalogs_goods_items = null;
        } else {
            $catalogs_goods_items = buildSidebarTree($catalogs_cur_good->items);
        }


//        dd($catalogs_services_items);

        return $view->with(compact('catalogs_goods_items'));
    }

}