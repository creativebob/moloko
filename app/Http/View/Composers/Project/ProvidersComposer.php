<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ProvidersComposer
{
    public function compose(View $view)
    {
        $prices_services = $view->prices_services;

        $collect = [];
        foreach($prices_services as $price_service) {
            $price_service->service_public->process->load('positions.staff');

            foreach($price_service->service_public->process->positions as $position) {
                foreach($position->staff as $staffer) {
                    $collect[] = $staffer;
                }
            }
        }
        $providers = collect($collect)->unique();
        $providers->load('user.photo');
        return $view->with(compact('providers'));
    }
}
