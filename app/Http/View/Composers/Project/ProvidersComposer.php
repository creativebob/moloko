<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ProvidersComposer
{
    public function compose(View $view)
    {
        $prices_services = $view->prices_services;

        // TODO - 28.02.20 - Костыль дял вывода исполнителей услуг
        $collect = [];
        foreach($prices_services as $price_service) {
            $price_service->service_public->process->load('positions.staff');

            foreach($price_service->service_public->process->positions as $position) {
                foreach($position->staff as $staffer) {
                    $collect[] = $staffer->load('user.photo');
                }
            }
        }
        $providers = collect($collect)->unique();
        return $view->with(compact('providers'));
    }
}
