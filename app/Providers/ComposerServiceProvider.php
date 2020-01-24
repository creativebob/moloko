<?php

namespace App\Providers;

use App\Http\ViewComposers\System\AccountsComposer;
use App\Http\ViewComposers\System\ArticlesCategoriesWithGroupsComposer;
use App\Http\ViewComposers\System\ArticlesCategoriesWithItemsComposer;
use App\Http\ViewComposers\System\ArticlesCategoriesWithItemsComposerForManufacturer;
use App\Http\ViewComposers\System\AttachmentsComposer;
use App\Http\ViewComposers\System\CatalogGoodsWithPricesComposer;
use App\Http\ViewComposers\System\ChannelsComposer;
use App\Http\ViewComposers\System\ChargesComposer;
use App\Http\ViewComposers\System\CitiesComposer;
use App\Http\ViewComposers\System\CitySearchComposer;
use App\Http\ViewComposers\System\ContainersCategoriesComposer;
use App\Http\ViewComposers\System\ContainersComposer;
use App\Http\ViewComposers\System\DirectiveCategoriesComposer;
use App\Http\ViewComposers\System\DisplayModesComposer;
use App\Http\ViewComposers\System\FiltersComposer;
use App\Http\ViewComposers\System\NotificationsComposer;
use App\Http\ViewComposers\System\ProcessesCategoriesWithGroupsComposer;
use App\Http\ViewComposers\System\SitesWIthFilialsAndCatalogsComposer;
use App\Http\ViewComposers\System\StocksComposer;
use App\Http\ViewComposers\System\WidgetsComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\System\SidebarComposer;
use App\Http\ViewComposers\System\SectorsSelectComposer;

use App\Http\ViewComposers\System\StagesComposer;

use App\Http\ViewComposers\System\CountriesComposer;

use App\Http\ViewComposers\System\FilialsForUserComposer;
use App\Http\ViewComposers\System\DepartmentsForUserComposer;

use App\Http\ViewComposers\System\UserFilialsComposer;
use App\Http\ViewComposers\System\CatalogsServicesItemsForFilialComposer;

use App\Http\ViewComposers\System\RolesComposer;

use App\Http\ViewComposers\System\LegalFormsSelectComposer;
use App\Http\ViewComposers\System\CheckerComposer;

use App\Http\ViewComposers\System\LoyaltiesComposer;

use App\Http\ViewComposers\System\UnitsCategoriesComposer;
use App\Http\ViewComposers\System\UnitsComposer;
use App\Http\ViewComposers\System\UnitsArticleComposer;
use App\Http\ViewComposers\System\UnitsProcessesComposer;

use App\Http\ViewComposers\System\SourceWithSourceServicesComposer;
use App\Http\ViewComposers\System\SourceServicesComposer;
use App\Http\ViewComposers\System\PeriodsComposer;
use App\Http\ViewComposers\System\BooklistTypesComposer;

use App\Http\ViewComposers\System\ManufacturersComposer;

use App\Http\ViewComposers\System\SupplierSelectComposer;
use App\Http\ViewComposers\System\ContragentsComposer;

use App\Http\ViewComposers\System\CategoriesSelectComposer;
use App\Http\ViewComposers\System\GoodsModesComposer;
use App\Http\ViewComposers\System\RawsModesComposer;

use App\Http\ViewComposers\System\CatalogsSelectComposer;

use App\Http\ViewComposers\System\AccordionsComposer;
use App\Http\ViewComposers\System\MenuViewComposer;
use App\Http\ViewComposers\System\DepartmentsComposer;
use App\Http\ViewComposers\System\DepartmentsViewComposer;
use App\Http\ViewComposers\System\FilialsComposer;
// use App\Http\ViewComposers\System\SectorsComposer;

use App\Http\ViewComposers\System\CategoriesComposer;

use App\Http\ViewComposers\System\GoodsCategoriesComposer;
use App\Http\ViewComposers\System\RawsCategoriesComposer;
use App\Http\ViewComposers\System\GoodsProductsComposer;
use App\Http\ViewComposers\System\RawsProductsComposer;
use App\Http\ViewComposers\System\AlbumsCategoriesSelectComposer;
use App\Http\ViewComposers\System\AlbumsComposer;

use App\Http\ViewComposers\System\AlignsComposer;
use App\Http\ViewComposers\System\NavigationsCategoriesSelectComposer;
use App\Http\ViewComposers\System\MenusSelectComposer;

use App\Http\ViewComposers\System\IndicatorsCategoriesSelectComposer;
use App\Http\ViewComposers\System\DirectionsComposer;

use App\Http\ViewComposers\System\UsersComposer;
use App\Http\ViewComposers\System\StaffComposer;
use App\Http\ViewComposers\System\EmptyStaffComposer;
use App\Http\ViewComposers\System\PositionsComposer;
use App\Http\ViewComposers\System\PropertiesComposer;

use App\Http\ViewComposers\System\SitesComposer;
use App\Http\ViewComposers\System\SiteMenusComposer;
use App\Http\ViewComposers\System\PagesComposer;

use App\Http\ViewComposers\System\CategoriesDrilldownComposer;

use App\Http\ViewComposers\System\EntitiesStatisticsSelectComposer;

use App\Http\ViewComposers\System\CatalogsServicesComposer;
use App\Http\ViewComposers\System\CatalogsServicesItemsComposer;
use App\Http\ViewComposers\System\FilialsForCatalogsServicesComposer;

use App\Http\ViewComposers\System\CatalogsGoodsComposer;
use App\Http\ViewComposers\System\CatalogsGoodsItemsComposer;
use App\Http\ViewComposers\System\FilialsForCatalogsGoodsComposer;

use App\Http\ViewComposers\System\CatalogsTypesComposer;

use App\Http\ViewComposers\System\ArticlesGroupsComposer;
use App\Http\ViewComposers\System\ProcessesGroupsComposer;

use App\Http\ViewComposers\System\RawsComposer;
use App\Http\ViewComposers\System\GoodsComposer;
use App\Http\ViewComposers\System\TmcComposer;

use App\Http\ViewComposers\System\WorkflowsComposer;
use App\Http\ViewComposers\System\ServicesComposer;

use App\Http\ViewComposers\System\LeftoverOperationsComposer;

use App\Http\ViewComposers\System\ProcessesTypesComposer;

use App\Http\ViewComposers\System\RoomsComposer;

use App\Http\ViewComposers\System\RubricatorsComposer;
use App\Http\ViewComposers\System\RubricatorsItemsComposer;


use App\Http\ViewComposers\System\ListChallengesComposer;
use App\Http\ViewComposers\System\LeadMethodsComposer;

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

        view()->composer([
            'prices_services.select_user_filials',
            'prices_goods.select_user_filials',
        ], UserFilialsComposer::class);
        view()->composer('prices_services.sync.modal', CatalogsServicesItemsForFilialComposer::class);

        view()->composer([
            'includes.selects.roles',
            'includes.lists.roles',
        ], RolesComposer::class);

        view()->composer('includes.lists.charges', ChargesComposer::class);
        view()->composer('includes.lists.widgets', WidgetsComposer::class);
        view()->composer('includes.lists.notifications', NotificationsComposer::class);

        view()->composer('includes.selects.legal_forms', LegalFormsSelectComposer::class);
        view()->composer('includes.inputs.checker', CheckerComposer::class);

        view()->composer('includes.selects.loyalties', LoyaltiesComposer::class);

        view()->composer([
            'system.common.includes.city_search',
            'test'
        ], CitySearchComposer::class);

        view()->composer([
            'system.common.includes.city_search',
        ], CitiesComposer::class);

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

        view()->composer('products.articles.common.edit.edit', ArticlesCategoriesWithGroupsComposer::class);
        view()->composer('products.processes.common.edit.edit', ProcessesCategoriesWithGroupsComposer::class);

        view()->composer('system.pages.consignments.edit', ArticlesCategoriesWithItemsComposer::class);
        view()->composer('system.pages.productions.edit', ArticlesCategoriesWithItemsComposerForManufacturer::class);

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
        view()->composer([
            'includes.selects.stocks',
            'leads.form'
        ], StocksComposer::class);

        // Conflict: то, что осталось в нижней части
        // view()->composer(['includes.selects.manufacturers', 'includes.lists.manufacturers'], ManufacturersComposer::class);

        view()->composer('includes.selects.goods_modes', GoodsModesComposer::class);
        view()->composer('includes.selects.raws_modes', RawsModesComposer::class);

        view()->composer('includes.selects.users', UsersComposer::class);
        view()->composer('includes.selects.staff', StaffComposer::class);
        view()->composer('includes.selects.empty_staff', EmptyStaffComposer::class);

        view()->composer('includes.selects.positions', PositionsComposer::class);
        view()->composer([
            'products.common.metrics.properties_list',
            'products.common.metrics.page'
        ], PropertiesComposer::class);

        view()->composer([
            'includes.selects.catalogs_chosen',
            'includes.selects.catalogs'
        ], CatalogsSelectComposer::class);

        view()->composer([
            'includes.lists.sites',
            'includes.selects.sites',
        ], SitesComposer::class);
        view()->composer('includes.lists.site_menus', SiteMenusComposer::class);

        view()->composer('system.pages.domains.plugins', AccountsComposer::class);


        // Страницы сайта
        view()->composer('includes.selects.pages', PagesComposer::class);


        // Select'ы категорий
        view()->composer('includes.selects.categories_select', CategoriesSelectComposer::class);

        view()->composer('includes.drilldowns.categories', CategoriesDrilldownComposer::class);


        // Стандартные шаблоны типа "меню"
        view()->composer('system.common.accordions.categories_list', AccordionsComposer::class);
        view()->composer('includes.menu_views.category_list', MenuViewComposer::class);
        view()->composer('departments.filials_list', DepartmentsViewComposer::class);
        view()->composer('includes.lists.departments', DepartmentsComposer::class);

        view()->composer([
            'includes.lists.filials',
            'menus.form'
        ], FilialsComposer::class);

         view()->composer('system.pages.promotions.form', SitesWIthFilialsAndCatalogsComposer::class);

        view()->composer([
            'includes.selects.categories',
            'products.articles.common.create.categories_select',
            'products.processes.common.create.categories_select'
        ], CategoriesComposer::class);

        view()->composer('includes.selects.goods_categories', GoodsCategoriesComposer::class);
        view()->composer('includes.selects.raws_categories', RawsCategoriesComposer::class);
        view()->composer('includes.selects.containers_categories', ContainersCategoriesComposer::class);
        view()->composer('includes.selects.goods_products', GoodsProductsComposer::class);
        view()->composer('includes.selects.raws_products', RawsProductsComposer::class);
        view()->composer([
            'includes.selects.albums_categories',
            'albums.select_albums_categories',
            'news.albums.modal_albums',
        ] , AlbumsCategoriesSelectComposer::class);
        view()->composer([
            'includes.selects.albums',
            'news.albums.select_albums',
        ], AlbumsComposer::class);

        view()->composer('includes.selects.aligns', AlignsComposer::class);
        view()->composer('includes.selects.navigations_categories', NavigationsCategoriesSelectComposer::class);
        view()->composer('includes.selects.menus', MenusSelectComposer::class);

        view()->composer('includes.selects.indicators_categories', IndicatorsCategoriesSelectComposer::class);
        view()->composer('includes.selects.directions', DirectionsComposer::class);

        view()->composer('includes.selects.entities_statistics', EntitiesStatisticsSelectComposer::class);

        view()->composer('products.processes.services.prices.catalogs', CatalogsServicesComposer::class);
        view()->composer('products.processes.services.prices.catalogs_items', CatalogsServicesItemsComposer::class);
        view()->composer('products.processes.services.prices.filials', FilialsForCatalogsServicesComposer::class);

        view()->composer([
            'products.articles.goods.prices.catalogs',
            'leads.catalogs.modal_catalogs_goods'
        ], CatalogsGoodsComposer::class);

        view()->composer('products.articles.goods.prices.catalogs_items', CatalogsGoodsItemsComposer::class);
        view()->composer('products.articles.goods.prices.filials', FilialsForCatalogsGoodsComposer::class);

        view()->composer('leads.form', CatalogGoodsWithPricesComposer::class);

        view()->composer('includes.selects.articles_groups', ArticlesGroupsComposer::class);
        view()->composer('includes.selects.processes_groups', ProcessesGroupsComposer::class);

        view()->composer([
            'products.articles_categories.goods_categories.raws.raws_list',
            'products.articles.goods.raws.raws_list'
        ], RawsComposer::class);

        view()->composer([
            'products.articles.goods.containers.containers_list'
        ], ContainersComposer::class);

        view()->composer([
            'products.articles.goods.attachments.attachments_list'
        ], AttachmentsComposer::class);

        view()->composer([
            'products.articles.goods.goods.goods_list'
        ], GoodsComposer::class);

        view()->composer([
            'products.processes_categories.services_categories.workflows.workflows_list',
            'products.processes.services.workflows.workflows_list'
        ], WorkflowsComposer::class);

        view()->composer([
            'products.processes.services.services.services_list'
        ], ServicesComposer::class);

        view()->composer('includes.selects.tmc', TmcComposer::class);
        view()->composer([
            'products.articles.goods.raws.leftover_operations_select',
            'products.articles.goods.containers.leftover_operations_select',
        ], LeftoverOperationsComposer::class);

        view()->composer('includes.selects.processes_types', ProcessesTypesComposer::class);

        view()->composer('includes.selects.rooms', RoomsComposer::class);

        view()->composer('news.rubricators.rubricators', RubricatorsComposer::class);
        view()->composer('news.rubricators.select_rubricators_items', RubricatorsItemsComposer::class);

        view()->composer('layouts.challenges_for_me', ListChallengesComposer::class);

        view()->composer('includes.selects.lead_methods', LeadMethodsComposer::class);

        view()->composer('includes.selects.channels', ChannelsComposer::class);

        view()->composer('includes.selects.display_modes', DisplayModesComposer::class);
        view()->composer('includes.selects.directive_categories', DirectiveCategoriesComposer::class);

        view()->composer('includes.lists.filters', FiltersComposer::class);


    }

    public function register()
    {
        //
    }
}
