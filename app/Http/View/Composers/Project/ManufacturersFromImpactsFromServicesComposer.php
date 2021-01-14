<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ManufacturersFromImpactsFromServicesComposer
{
    public function compose(View $view)
    {
        $pricesServices = $view->catalogs_services_item->prices;
        $pricesServices->load([
            'service.process.impacts.article.manufacturer.company'
        ]);

        if ($pricesServices) {
            $collect = [];
            foreach($pricesServices as $priceService) {
                foreach($priceService->service->process->impacts as $impact) {
                    $collect[] = $impact->article->manufacturer;
                }
            }
            $manufacturers = collect($collect)->unique();
        }
//        dd($manufacturers);

        return $view->with(compact('manufacturers'));
    }
}
