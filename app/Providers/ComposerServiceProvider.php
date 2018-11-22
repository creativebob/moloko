<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\SidebarComposer;
use App\Http\ViewComposers\SectorsSelectComposer;
use App\Http\ViewComposers\CountriesSelectComposer;
use App\Http\ViewComposers\LegalFormsSelectComposer;
use App\Http\ViewComposers\ServicesTypesCheckboxComposer;

use App\Http\ViewComposers\UnitsCategoriesComposer;
use App\Http\ViewComposers\UnitsComposer;

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

        view()->composer(['includes.selects.units_categories'], UnitsCategoriesComposer::class);
        view()->composer(['includes.selects.units'], UnitsComposer::class);
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
