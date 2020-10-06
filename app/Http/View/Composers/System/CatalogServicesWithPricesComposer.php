<?php

namespace App\Http\View\Composers\System;

use App\CatalogsService;
use Illuminate\View\View;

class CatalogServicesWithPricesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cg = operator_right('catalogs_services', true, getmethod('index'));

        $catalogs_services = CatalogsService::with([
            'items:id,catalogs_service_id,name,photo_id,parent_id',
            'prices' => function ($q) {
                $q->with([
                    'service' => function($q) {
                        $q->with([
                            'process' => function ($q) {
                                $q->with([
                                    'photo',
                                    'manufacturer'
                                ])
                                ->where('draft', false)
                                ->select([
                                    'id',
                                    'name',
                                    'photo_id',
                                    'manufacturer_id',
                                    'draft'
                                ]);
                            }
                        ])
                            ->where('archive', false)
                            ->select([
                                'id',
                                'process_id',
                            ]);
                    },
                    'currency',
                    'discounts_actual',
                    'catalogs_item.discounts_actual'
                ])
                    ->whereHas('service', function ($q) {
                        $q->where('archive', false)
                            ->whereHas('process', function ($q) {
                                $q->where('draft', false);
                            });
                    })
                    ->where([
                        'archive' => false,
                        'filial_id' => \Auth::user()->StafferFilialId
                    ])
                    ->select([
                        'prices_services.id',
                        'archive',
                        'prices_services.catalogs_service_id',
                        'catalogs_services_item_id',
                        'price',
                        'service_id',
                        'filial_id'
                    ]);
            },
        ])
            ->moderatorLimit($answer_cg)
            ->companiesLimit($answer_cg)
            ->authors($answer_cg)
            ->filials($answer_cg)
            ->whereHas('filials', function ($q) {
                $q->where('id', auth()->user()->stafferFilialId);
            })
            ->get();
//         dd($сatalogs_services);

        $catalogs_services_items = [];
        $catalogs_services_prices = [];
        foreach ($catalogs_services as $catalog_services) {
            $catalogs_services_items = array_merge($catalogs_services_items, buildTreeArray($catalog_services->items));
            $catalogs_services_prices = array_merge($catalogs_services_prices, $catalog_services->prices->toArray());
        }
//        dd($catalogs_services_prices);

        $catalogs_services_data = [
            'catalogsServices' => $catalogs_services,
            'catalogsServicesItems' => $catalogs_services_items,
            'catalogsServicesPrices' => $catalogs_services_prices

        ];

        return $view->with(compact('catalogs_services_data'));
    }

}
