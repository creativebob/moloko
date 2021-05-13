<?php

namespace App\Providers\Project\View;

use App\Http\View\Composers\Project\AdditionalsSeosComposer;
use App\Http\View\Composers\Project\AlbumByAliasComposer;
use App\Http\View\Composers\Project\CatalogsServiceComposer;
use App\Http\View\Composers\Project\CatalogsGoodsComposer;
use App\Http\View\Composers\Project\DiscountsForEstimatesComposer;
use App\Http\View\Composers\Project\DisplayModesComposer;
use App\Http\View\Composers\Project\ImpactsFromOwnersFromPricesServicesFromCatalogsServicesItemComposer;
use App\Http\View\Composers\Project\ImpactsFromPricesServicesFromCatalogsServicesItemComposer;
use App\Http\View\Composers\Project\ManufacturersFromImpactsFromServicesComposer;
use App\Http\View\Composers\Project\ManufacturersFromOwnersImpactsFromServicesComposer;
use App\Http\View\Composers\Project\NavigationByAlignComposer;
use App\Http\View\Composers\Project\NavigationsComposer;
use App\Http\View\Composers\Project\PricesGoodsFilterComposer;
use App\Http\View\Composers\Project\ProvidersComposer;
use App\Http\View\Composers\Project\ServicesComposer;
use App\Http\View\Composers\Project\ServicesFlowsComposer;
use App\Http\View\Composers\Project\StaffComposer;
use App\Http\View\Composers\Project\ClientsCompaniesListComposer;
use App\Http\View\Composers\Project\ManufacturersListComposer;
use App\Http\View\Composers\Project\NewsComposer;
use App\Http\View\Composers\Project\PromotionsSliderComposer;
use App\Http\View\Composers\Project\SubCatalogsGoodsItemsComposer;
use App\Http\View\Composers\Project\ToolsCategoriesWithToolsComposer;
use App\Http\View\Composers\Project\VendorsComposer;
use App\Http\View\Composers\Project\WorktimeFilialTodayComposer;
use App\Http\View\Composers\Project\PluginsComposer;
use App\Http\View\Composers\Project\WorktimeTodayComposer;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer( 'project.composers.menus.menu', NavigationsComposer::class);
        view()->composer('project.composers.navigations.navigation_by_align', NavigationByAlignComposer::class);

        view()->composer([
            'project.composers.catalogs_services.accordion',
            'project.composers.catalogs_services.menu_one_level',
            'project.composers.catalogs_services.sidebar',
        ], CatalogsServiceComposer::class);

        view()->composer([
            'project.composers.catalogs_goods.accordion',
            'project.composers.catalogs_goods.menu',
            'project.composers.catalogs_goods.images_menu',
            'project.composers.catalogs_goods.sidebar',
            'project.composers.catalogs_goods.nav_catalog_goods'
        ], CatalogsGoodsComposer::class);

        view()->composer('project.composers.catalogs_goods_items.sub_catalogs_goods_items', SubCatalogsGoodsItemsComposer::class);

        view()->composer([
            'project.composers.news.images'
        ], NewsComposer::class);

        view()->composer([
            'project.composers.staff.section',
            'project.composers.staff.list',
        ], StaffComposer::class);

        view()->composer('project.composers.services.section', ServicesComposer::class);
        view()->composer('project.composers.services_flows.section', ServicesFlowsComposer::class);

        view()->composer('project.composers.schedules.worktime_filial_today', WorktimeFilialTodayComposer::class);
        view()->composer('project.composers.worktimes.today', WorktimeTodayComposer::class);
        view()->composer('project.composers.plugins.list', PluginsComposer::class);

        view()->composer('project.composers.prices_goods.sidebar_filters', PricesGoodsFilterComposer::class);

        view()->composer([
            'project.composers.clients.companies_list',
            'project.composers.clients.section'
        ], ClientsCompaniesListComposer::class);

        view()->composer('project.composers.promotions.slider', PromotionsSliderComposer::class);

        view()->composer([
            'project.composers.manufacturers.list',
            'project.composers.manufacturers.section'
        ], ManufacturersListComposer::class);

        view()->composer([
            'project.composers.vendors.section'
        ], VendorsComposer::class);

        view()->composer('project.composers.display_modes.section', DisplayModesComposer::class);
        view()->composer('project.composers.tools_categories.sidebar_with_items', ToolsCategoriesWithToolsComposer::class);
        view()->composer('project.composers.prices_services.providers', ProvidersComposer::class);
        view()->composer('project.composers.prices_services.impacts', ImpactsFromPricesServicesFromCatalogsServicesItemComposer::class);
        view()->composer('project.composers.prices_services.impacts_from_owners', ImpactsFromOwnersFromPricesServicesFromCatalogsServicesItemComposer::class);

        view()->composer('project.composers.manufacturers.manufacturers_from_impacts_from_services', ManufacturersFromImpactsFromServicesComposer::class);
        view()->composer('project.composers.manufacturers.manufacturers_from_owners_impacts_from_services', ManufacturersFromOwnersImpactsFromServicesComposer::class);

        view()->composer('project.composers.albums.album_by_alias', AlbumByAliasComposer::class);

        view()->composer('project.layouts.inheads.inhead_with_additionals', AdditionalsSeosComposer::class);

//        view()->composer('project.composers.estimates.discounts.header_component', DiscountsForEstimatesComposer::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
