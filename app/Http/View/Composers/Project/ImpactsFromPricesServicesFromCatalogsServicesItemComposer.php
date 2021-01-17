<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ImpactsFromPricesServicesFromCatalogsServicesItemComposer
{
    public function compose(View $view)
    {
        $catalogsServicesItem = $view->catalogs_services_item;
        $catalogsServicesItem->prices()
            ->with([
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
                                });

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
            ->filter()
            ->oldest('sort')
            ->get();

        $pricesServices = $catalogsServicesItem->prices;

        return $view->with(compact('pricesServices'));
    }
}
