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
use App\Http\ViewComposers\CategoriesSelectComposer;
use App\Http\ViewComposers\GoodsModesComposer;
use App\Http\ViewComposers\RawsModesComposer;

use App\Http\ViewComposers\MenuViewComposer;
// use App\Http\ViewComposers\SectorsComposer;
use App\Http\ViewComposers\GoodsCategoriesComposer;
// use App\Http\ViewComposers\AlbumsCategoriesComposer;


class ComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        view()->composer('layouts.sidebar', SidebarComposer::class);
        view()->composer('includes.selects.sectors_select', SectorsSelectComposer::class);
        view()->composer('includes.selects.countries', CountriesSelectComposer::class);
        view()->composer('includes.selects.legal_forms', LegalFormsSelectComposer::class);
        view()->composer('includes.inputs.checker', CheckerComposer::class);

        view()->composer(['includes.selects.units_categories'], UnitsCategoriesComposer::class);
        view()->composer(['includes.selects.units'], UnitsComposer::class);

        view()->composer('includes.selects.manufacturers', ManufacturersComposer::class);

        view()->composer('includes.selects.goods_modes', GoodsModesComposer::class);
        view()->composer('includes.selects.raws_modes', RawsModesComposer::class);

        // Select'ы категорий
        view()->composer('includes.selects.categories_select', CategoriesSelectComposer::class);


        // Стандартные шаблоны типа "меню"
        view()->composer('includes.menu_views.category_list', MenuViewComposer::class);
        // view()->composer('includes.selects.sectors', SectorsComposer::class);
        view()->composer('includes.selects.goods_categories', GoodsCategoriesComposer::class);
        // view()->composer('includes.selects.albums_categories', AlbumsCategoriesComposer::class);
    }

    public function register()
    {
        //
    }
}
