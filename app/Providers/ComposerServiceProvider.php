<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\SidebarComposer;
use App\Http\ViewComposers\SectorsSelectComposer;
use App\Http\ViewComposers\CountriesSelectComposer;
use App\Http\ViewComposers\LegalFormsSelectComposer;
use App\Http\ViewComposers\CheckerComposer;

use App\Http\ViewComposers\UnitsCategoriesComposer;
use App\Http\ViewComposers\UnitsComposer;

use App\Http\ViewComposers\ManufacturersComposer;
use App\Http\ViewComposers\ViewMenuComposer;
use App\Http\ViewComposers\SectorsComposer;

class ComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        view()->composer('layouts.sidebar', SidebarComposer::class);
        // view()->composer('includes.selects.sectors', SectorsSelectComposer::class);
        view()->composer('includes.selects.countries', CountriesSelectComposer::class);
        view()->composer('includes.selects.legal_forms', LegalFormsSelectComposer::class);
        view()->composer('includes.inputs.checker', CheckerComposer::class);

        view()->composer(['includes.selects.units_categories'], UnitsCategoriesComposer::class);
        view()->composer(['includes.selects.units'], UnitsComposer::class);

        view()->composer('includes.selects.manufacturers', ManufacturersComposer::class);

        view()->composer('includes.menu_views.category_list', ViewMenuComposer::class);

        view()->composer('includes.selects.sectors', SectorsComposer::class);
    }

    public function register()
    {
        //
    }
}
