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
use App\Http\ViewComposers\UnitsArticleComposer;
use App\Http\ViewComposers\UnitsProcessesComposer;

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

use App\Http\ViewComposers\AccordionsComposer;
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

use App\Http\ViewComposers\CatalogsGoodsComposer;

use App\Http\ViewComposers\CatalogsServicesComposer;
use App\Http\ViewComposers\CatalogsServicesItemsComposer;

use App\Http\ViewComposers\CatalogsTypesComposer;

use App\Http\ViewComposers\ArticlesGroupsComposer;
use App\Http\ViewComposers\ProcessesGroupsComposer;

use App\Http\ViewComposers\RawsComposer;
use App\Http\ViewComposers\TmcComposer;

use App\Http\ViewComposers\WorkflowsComposer;

use App\Http\ViewComposers\LeftoverOperationsComposer;

use App\Http\ViewComposers\ProcessesTypesComposer;

use App\Http\ViewComposers\RoomsComposer;

use App\Http\ViewComposers\RubricatorsComposer;
use App\Http\ViewComposers\RubricatorsItemsComposer;

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
        view()->composer([
            'includes.selects.units',
            'includes.selects.units_extra',
        ], UnitsComposer::class);

        view()->composer('products.articles.common.edit.select_units', UnitsArticleComposer::class);
        view()->composer('products.processes.common.edit.select_units', UnitsProcessesComposer::class);

        view()->composer(['includes.selects.source_with_source_services'], SourceWithSourceServicesComposer::class);
        view()->composer(['includes.selects.source_services'], SourceServicesComposer::class);

        view()->composer(['includes.selects.periods'], PeriodsComposer::class);

        view()->composer(['includes.selects.booklist_types'], BooklistTypesComposer::class);

        view()->composer([
            'includes.selects.manufacturers',
            'includes.selects.manufacturers_with_placeholder',
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
        view()->composer('products.articles_categories.goods_categories.metrics.properties_list', PropertiesComposer::class);

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
        view()->composer('common.accordions.categories_list', AccordionsComposer::class);
        view()->composer('includes.menu_views.category_list', MenuViewComposer::class);
        view()->composer('departments.filials_list', DepartmentsViewComposer::class);
        view()->composer('includes.lists.departments', DepartmentsComposer::class);
        // view()->composer('includes.selects.sectors', SectorsComposer::class);

        view()->composer([
            'includes.selects.categories',
            'products.articles.common.create.categories_select',
            'products.processes.common.create.categories_select'
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

        view()->composer('products.articles.goods.catalogs_with_items', CatalogsGoodsComposer::class);
        view()->composer('products.processes.services.prices.catalogs', CatalogsServicesComposer::class);
        view()->composer('products.processes.services.prices.catalogs_items', CatalogsServicesItemsComposer::class);

        view()->composer('includes.selects.articles_groups', ArticlesGroupsComposer::class);
        view()->composer('includes.selects.processes_groups', ProcessesGroupsComposer::class);

        view()->composer([
            'products.articles_categories.goods_categories.raws.raws_list',
            'products.articles.goods.raws.raws_list'
        ], RawsComposer::class);

        view()->composer([
            'products.processes_categories.services_categories.workflows.workflows_list',
            'products.processes.services.workflows.workflows_list'
        ], WorkflowsComposer::class);

        view()->composer('includes.selects.tmc', TmcComposer::class);
        view()->composer('products.articles.goods.raws.leftover_operations_select', LeftoverOperationsComposer::class);

        view()->composer('includes.selects.processes_types', ProcessesTypesComposer::class);

        view()->composer('includes.selects.rooms', RoomsComposer::class);

        view()->composer('news.rubricators.select_rubricators', RubricatorsComposer::class);
        view()->composer('news.rubricators.select_rubricators_items', RubricatorsItemsComposer::class);


    }

    public function register()
    {
        //
    }
}
