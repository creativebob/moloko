<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class CatalogsServiceComposer
{
	public function compose(View $view)
	{

	    $site = $view->site->load([
	        'catalogs_services' => function ($q) {
                $q->with([
                    'items'
                ])
                ->where([
                    'display' => true
                ])
                ->orderBy('sort');
            }
        ]);

        $catalogs_service = $site->catalogs_services->first();
//        dd($catalogs_service);

        if (is_null($catalogs_service)) {
            $catalogs_services_items = null;
        } else {
            $catalogs_services_items = buildSidebarTree($catalogs_service->items);
        }



//        dd($catalogs_services_items);

        return $view->with(compact('catalogs_services_items'));
    }

}
