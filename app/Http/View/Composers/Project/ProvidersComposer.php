<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ProvidersComposer
{
    public function compose(View $view)
    {
        $prices_services = $view->prices_services;
        $providers = $prices_services->service->process->positions->staff->nique();
        return $view->with(compact('providers'));
    }

}
