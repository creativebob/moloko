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
use App\Http\ViewComposers\PeriodsComposer;


use App\Http\ViewComposers\ManufacturersComposer;
use App\Http\ViewComposers\SupplierSelectComposer;
use App\Http\ViewComposers\ContragentsComposer;

use App\Http\ViewComposers\CategoriesSelectComposer;
use App\Http\ViewComposers\GoodsModesComposer;
use App\Http\ViewComposers\RawsModesComposer;

use App\Http\ViewComposers\CatalogsSelectComposer;

use App\Http\ViewComposers\MenuViewComposer;
use App\Http\ViewComposers\DepartmentsComposer;
use App\Http\ViewComposers\DepartmentsViewComposer;
// use App\Http\ViewComposers\SectorsComposer;
use App\Http\ViewComposers\GoodsCategoriesComposer;
use App\Http\ViewComposers\RawsCategoriesComposer;
use App\Http\ViewComposers\GoodsProductsComposer;
use App\Http\ViewComposers\RawsProductsComposer;
use App\Http\ViewComposers\AlbumsCategoriesSelectComposer;
use App\Http\ViewComposers\AlbumsComposer;

use App\Http\ViewComposers\indicatorsCategoriesSelectComposer;
use App\Http\ViewComposers\DirectionsComposer;

use App\Http\ViewComposers\UsersComposer;
use App\Http\ViewComposers\StaffComposer;
use App\Http\ViewComposers\PositionsComposer;
use App\Http\ViewComposers\PropertiesComposer;

use App\Http\ViewComposers\SiteMenusComposer;

use App\Http\ViewComposers\CategoriesDrilldownComposer;

use App\Http\ViewComposers\EntitiesStatisticsSelectComposer;


class ComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        view()->composer('layouts.sidebar', SidebarComposer::class);
        view()->composer('includes.selects.sectors_select', SectorsSelectComposer::class);
        view()->composer('includes.selects.countries', CountriesSelectComposer::class);
        view()->composer('includes.selects.legal_forms', LegalFormsSelectComposer::class);
        view()->composer('includes.inputs.checker', CheckerComposer::class);

        view()->composer('includes.inputs.checker_contragents', ContragentsComposer::class);

        view()->composer(['includes.selects.units_categories'], UnitsCategoriesComposer::class);
        view()->composer(['includes.selects.units'], UnitsComposer::class);
        view()->composer(['includes.selects.periods'], PeriodsComposer::class);

        view()->composer('includes.selects.manufacturers', ManufacturersComposer::class);
        view()->composer('includes.selects.suppliers', SupplierSelectComposer::class);

        // Conflict: то, что осталось в нижней части
        // view()->composer(['includes.selects.manufacturers', 'includes.lists.manufacturers'], ManufacturersComposer::class);


        view()->composer('includes.selects.goods_modes', GoodsModesComposer::class);
        view()->composer('includes.selects.raws_modes', RawsModesComposer::class);

        view()->composer('includes.selects.users', UsersComposer::class);
        view()->composer('includes.selects.staff', StaffComposer::class);

        view()->composer('includes.selects.positions', PositionsComposer::class);
        view()->composer('includes.metrics_category.properties_list', PropertiesComposer::class);

        view()->composer(['includes.selects.catalogs_chosen', 'includes.selects.catalogs'], CatalogsSelectComposer::class);

        view()->composer('includes.lists.site_menus', SiteMenusComposer::class);



        // Select'ы категорий
        view()->composer('includes.selects.categories_select', CategoriesSelectComposer::class);

        view()->composer('includes.drilldowns.categories', CategoriesDrilldownComposer::class);


        // Стандартные шаблоны типа "меню"
        view()->composer('includes.menu_views.category_list', MenuViewComposer::class);
        view()->composer('departments.filials_list', DepartmentsViewComposer::class);
        view()->composer('includes.lists.departments', DepartmentsComposer::class);
        // view()->composer('includes.selects.sectors', SectorsComposer::class);
        view()->composer('includes.selects.goods_categories', GoodsCategoriesComposer::class);
        view()->composer('includes.selects.raws_categories', RawsCategoriesComposer::class);
        view()->composer('includes.selects.goods_products', GoodsProductsComposer::class);
        view()->composer('includes.selects.raws_products', RawsProductsComposer::class);
        view()->composer('includes.selects.albums_categories', AlbumsCategoriesSelectComposer::class);
        view()->composer('includes.selects.albums', AlbumsComposer::class);

        view()->composer('includes.selects.indicators_categories', IndicatorsCategoriesSelectComposer::class);
        view()->composer('includes.selects.directions', DirectionsComposer::class);

        view()->composer('includes.selects.entities_statistics', EntitiesStatisticsSelectComposer::class);
    }

    public function register()
    {
        //
    }
}
