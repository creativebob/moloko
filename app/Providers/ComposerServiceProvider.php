<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\SidebarComposer;
use App\Http\ViewComposers\SectorsSelectComposer;
use App\Http\ViewComposers\CountriesSelectComposer;
use App\Http\ViewComposers\LegalFormsSelectComposer;
use App\Http\ViewComposers\ServicesTypesCheckboxComposer;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.sidebar', SidebarComposer::class);
        view()->composer('includes.selects.sectors', SectorsSelectComposer::class);
        view()->composer('includes.selects.countries', CountriesSelectComposer::class);
        view()->composer('includes.selects.legal_forms', LegalFormsSelectComposer::class);
        view()->composer('includes.checkboxers.checkboxer_services_types', ServicesTypesCheckboxComposer::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
