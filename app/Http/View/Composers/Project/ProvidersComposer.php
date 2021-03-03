<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ProvidersComposer
{
    public function compose(View $view)
    {
        $prices_services = $view->prices_services;
        $providers = collect();

        if ($prices_services) {
            // TODO - 28.02.20 - Костыль дял вывода исполнителей услуг
            $collect = [];
            foreach($prices_services as $price_service) {
                foreach($price_service->service->process->positions as $position) {
                    foreach($position->actual_staff->where('display', true) as $staffer) {
                        $collect[] = $staffer;
                    }
                }
            }
            $providers = collect($collect)->unique();
        }

        return $view->with(compact('providers'));
    }
}
