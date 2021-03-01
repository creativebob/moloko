<?php

namespace App\Http\View\Composers\Project;

use App\Models\Project\PricesService;
use Illuminate\View\View;

class ImpactsFromPricesServicesFromCatalogsServicesItemComposer
{
    public function compose(View $view)
    {
        $catalogsServicesItem = $view->catalogs_services_item;
        $pricesServicesWithImpacts = PricesService::with([
            'service.process' => function ($q) {
                $q->with([
                    'impacts' => function ($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'photo',
                                    'manufacturer.company' => function ($q) {
                                        $q->with([
                                            'color',
                                            'photo'
                                        ]);
                                    }
                                ]);
                            },

                        ])
                            ->where([
                                'display' => true,
                                'archive' => false
                            ])
                            ->whereHas('article', function ($q) {
                                $q->where('draft', false);
                            })

                            ->when(request()->brand, function ($q) {
                                $q->whereHas('article.manufactutrer.company', function ($q) {
                                    $q->where('name', request()->brand);
                                });
                            })
                        ;

                    }
                ]);
            }
        ])
            ->whereHas('service', function ($q) {
                $q->where([
                    'display' => true,
                    'archive' => false
                ])
                    ->whereHas('process', function ($q) {
                        $q->where('draft', false)
                            ->has('impacts');
                    });
            })
            ->where('is_hit', true)
            ->where('catalogs_services_item_id', $catalogsServicesItem->id)
            ->filter()
            ->oldest('sort')
            ->get();
//        dd($pricesServicesWithImpacts);

        return $view->with(compact('pricesServicesWithImpacts'));
    }
}
