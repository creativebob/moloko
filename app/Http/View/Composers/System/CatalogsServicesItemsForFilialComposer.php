<?php

namespace App\Http\View\Composers\System;

use App\CatalogsServicesItem;
use App\PricesService;

use Illuminate\View\View;

class CatalogsServicesItemsForFilialComposer
{
	public function compose(View $view)
	{
        $filial_id = $view->filial_id;

        $catalogs_items = CatalogsServicesItem::where('catalogs_service_id', $view->catalog_id)
        ->whereHas('prices_services', function ($q) use ($filial_id) {
            $q->where([
                'filial_id' => null,
                'archive' => false
            ]);
        })
        ->get();
        // dd($catalogs_items);

        $catalogs_items_ids = $catalogs_items->pluck('id')->toArray();
        // dd($catalogs_ids);

        $grouped_prices_services = PricesService::with([
            'service.process',
            'follower' => function ($q) use ($filial_id) {
            $q->where('filial_id', $filial_id);
            }
        ])
        ->where([
            'filial_id' => null,
            'archive' => false
        ])
        ->whereIn('catalogs_services_item_id', $catalogs_items_ids)
        ->get()
        ->groupBy('catalogs_services_item_id');
//         dd($grouped_prices_services);

        return $view->with(compact('catalogs_items', 'grouped_prices_services'));

    }
}
