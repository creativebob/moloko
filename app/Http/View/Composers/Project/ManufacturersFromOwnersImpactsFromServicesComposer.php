<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ManufacturersFromOwnersImpactsFromServicesComposer
{
    public function compose(View $view)
    {
        $manufacturers = collect();
        if ($view->catalogs_services_item) {
            $pricesServices = $view->catalogs_services_item->prices;
            $pricesServices->load([
                'service.process.impacts.article.owners.manufacturer.company'
            ]);

            if ($pricesServices) {
                $collect = [];
                foreach($pricesServices as $priceService) {
                    foreach($priceService->service->process->impacts as $impact) {
                        foreach ($impact->article->owners as $owner) {
                            if ($owner->manufacturer) {
                                $collect[] = $owner->manufacturer;
                            }
                        }

                    }
                }
                $manufacturers = collect($collect)->unique();
            }
        }
//        dd($manufacturers);

        return $view->with(compact('manufacturers'));
    }
}
