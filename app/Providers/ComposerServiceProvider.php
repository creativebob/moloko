<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\SidebarComposer;
use App\Http\ViewComposers\SectorsSelectComposer;

use App\Http\ViewComposers\StagesComposer;

use App\Http\ViewComposers\CountriesComposer;

use App\Http\ViewComposers\FilialsForUserComposer;
use App\Http\ViewComposers\DepartmentsForUserComposer;

use App\Http\ViewComposers\RolesComposer;

use App\Http\ViewComposers\LegalFormsSelectComposer;
use App\Http\ViewComposers\CheckerComposer;

use App\Http\ViewComposers\LoyaltiesComposer;

use App\Http\ViewComposers\UnitsCategoriesComposer;
use App\Http\ViewComposers\UnitsComposer;
use App\Http\ViewComposers\SourceWithSourceServicesComposer;
use App\Http\ViewComposers\SourceServicesComposer;
use App\Http\ViewComposers\PeriodsComposer;
use App\Http\ViewComposers\BooklistTypesComposer;

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

use App\Http\ViewComposers\CategoriesComposer;

use App\Http\ViewComposers\GoodsCategoriesComposer;
use App\Http\ViewComposers\RawsCategoriesComposer;
use App\Http\ViewComposers\GoodsProductsComposer;
use App\Http\ViewComposers\RawsProductsComposer;
use App\Http\ViewComposers\AlbumsCategoriesSelectComposer;
use App\Http\ViewComposers\AlbumsComposer;

use App\Http\ViewComposers\AlignsComposer;
use App\Http\ViewComposers\NavigationsCategoriesSelectComposer;
use App\Http\ViewComposers\MenusSelectComposer;

use App\Http\ViewComposers\IndicatorsCategoriesSelectComposer;
use App\Http\ViewComposers\DirectionsComposer;

use App\Http\ViewComposers\UsersComposer;
use App\Http\ViewComposers\StaffComposer;
use App\Http\ViewComposers\PositionsComposer;
use App\Http\ViewComposers\PropertiesComposer;

use App\Http\ViewComposers\SitesComposer;
use App\Http\ViewComposers\SiteMenusComposer;
use App\Http\ViewComposers\PagesComposer;

use App\Http\ViewComposers\CategoriesDrilldownComposer;

use App\Http\ViewComposers\EntitiesStatisticsSelectComposer;

use App\Http\ViewComposers\CatalogsComposer;
use App\Http\ViewComposers\CatalogsTypesComposer;

use App\Http\ViewComposers\ArticlesGroupsComposer;
use App\Http\ViewComposers\ProcessesGroupsComposer;

use App\Http\ViewComposers\CompositionsComposer;
use App\Http\ViewComposers\TmcComposer;

use App\Http\ViewComposers\WorkflowsComposer;

use App\Http\ViewComposers\LeftoverOperationsComposer;

class ComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        view()->composer('layouts.sidebar', SidebarComposer::class);
        view()->composer('includes.selects.sectors_select', SectorsSelectComposer::class);

        view()->composer('includes.selects.countries', CountriesComposer::class);
        view()->composer('includes.selects.stages', StagesComposer::class);

        view()->composer('includes.selects.filials_for_user', FilialsForUserComposer::class);
        view()->composer('includes.selects.departments_for_user', DepartmentsForUserComposer::class);

        view()->composer('includes.selects.roles', RolesComposer::class);

        view()->composer('includes.selects.legal_forms', LegalFormsSelectComposer::class);
        view()->composer('includes.inputs.checker', CheckerComposer::class);

        view()->composer('includes.selects.loyalties', LoyaltiesComposer::class);

        view()->composer([
            'includes.inputs.checker_contragents',
            'includes.selects.contragents'
        ], ContragentsComposer::class);

        view()->composer(['includes.selects.units_categories'], UnitsCategoriesComposer::class);
        view()->composer(['includes.selects.units'], UnitsComposer::class);

        view()->composer(['includes.selects.source_with_source_services'], SourceWithSourceServicesComposer::class);
        view()->composer(['includes.selects.source_services'], SourceServicesComposer::class);

        view()->composer(['includes.selects.periods'], PeriodsComposer::class);

        view()->composer(['includes.selects.booklist_types'], BooklistTypesComposer::class);

        view()->composer([
            'includes.selects.manufacturers',
            'includes.lists.manufacturers'
        ], ManufacturersComposer::class);

        view()->composer('includes.selects.suppliers', SupplierSelectComposer::class);

        // Conflict: то, что осталось в нижней части
        // view()->composer(['includes.selects.manufacturers', 'includes.lists.manufacturers'], ManufacturersComposer::class);

        view()->composer('includes.selects.goods_modes', GoodsModesComposer::class);
        view()->composer('includes.selects.raws_modes', RawsModesComposer::class);

        view()->composer('includes.selects.users', UsersComposer::class);
        view()->composer('includes.selects.staff', StaffComposer::class);

        view()->composer('includes.selects.positions', PositionsComposer::class);
        view()->composer('goods_categories.metrics.properties_list', PropertiesComposer::class);

        view()->composer([
            'includes.selects.catalogs_chosen',
            'includes.selects.catalogs'
        ], CatalogsSelectComposer::class);

        view()->composer('includes.lists.sites', SitesComposer::class);
        view()->composer('includes.lists.site_menus', SiteMenusComposer::class);


        // Страницы сайта
        view()->composer('includes.selects.pages', PagesComposer::class);


        // Select'ы категорий
        view()->composer('includes.selects.categories_select', CategoriesSelectComposer::class);

        view()->composer('includes.drilldowns.categories', CategoriesDrilldownComposer::class);


        // Стандартные шаблоны типа "меню"
        view()->composer('includes.menu_views.category_list', MenuViewComposer::class);
        view()->composer('departments.filials_list', DepartmentsViewComposer::class);
        view()->composer('includes.lists.departments', DepartmentsComposer::class);
        // view()->composer('includes.selects.sectors', SectorsComposer::class);

        view()->composer([
            'includes.selects.categories',
            'tmc.create.categories_select',
            'processes.create.categories_select'
        ], CategoriesComposer::class);

        view()->composer('includes.selects.goods_categories', GoodsCategoriesComposer::class);
        view()->composer('includes.selects.raws_categories', RawsCategoriesComposer::class);
        view()->composer('includes.selects.goods_products', GoodsProductsComposer::class);
        view()->composer('includes.selects.raws_products', RawsProductsComposer::class);
        view()->composer('includes.selects.albums_categories', AlbumsCategoriesSelectComposer::class);
        view()->composer('includes.selects.albums', AlbumsComposer::class);

        view()->composer('includes.selects.aligns', AlignsComposer::class);
        view()->composer('includes.selects.navigations_categories', NavigationsCategoriesSelectComposer::class);
        view()->composer('includes.selects.menus', MenusSelectComposer::class);

        view()->composer('includes.selects.indicators_categories', IndicatorsCategoriesSelectComposer::class);
        view()->composer('includes.selects.directions', DirectionsComposer::class);

        view()->composer('includes.selects.entities_statistics', EntitiesStatisticsSelectComposer::class);

        view()->composer('includes.catalogs_with_items', CatalogsComposer::class);
        view()->composer('includes.selects.catalogs_types', CatalogsTypesComposer::class);

        view()->composer('includes.selects.articles_groups', ArticlesGroupsComposer::class);
        view()->composer('includes.selects.processes_groups', ProcessesGroupsComposer::class);

        view()->composer([
            'goods_categories.compositions.compositions_list',
            'goods.compositions.compositions_list'
        ], CompositionsComposer::class);

        view()->composer([
            'services_categories.workflows.workflows_list',
            'services.workflows.workflows_list'
        ], WorkflowsComposer::class);

        view()->composer('includes.selects.tmc', TmcComposer::class);
        view()->composer('goods.compositions.leftover_operations_select', LeftoverOperationsComposer::class);


    }

    public function register()
    {
        //
    }
}
