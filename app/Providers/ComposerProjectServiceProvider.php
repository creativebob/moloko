<?php

namespace App\Providers;

use App\Domain;
use App\Http\View\Composers\Project\CartComposer;
use App\Http\View\Composers\Project\CatalogsGoodsItemsFilterComposer;
use App\Http\View\Composers\Project\DepartmentsComposer;
use App\Http\View\Composers\Project\CatalogsServiceComposer;
use App\Http\View\Composers\Project\CatalogsGoodsComposer;
use App\Http\View\Composers\Project\DisplayModesComposer;
use App\Http\View\Composers\Project\FilialComposer;
use App\Http\View\Composers\Project\NavigationsComposer;
use App\Http\View\Composers\Project\PricesGoodsPriceFilterComposer;
use App\Http\View\Composers\Project\PricesGoodsRawsArticlesGroupsFilterComposer;
use App\Http\View\Composers\Project\PricesGoodsWeightFilterComposer;
use App\Http\View\Composers\Project\ProvidersComposer;
use App\Http\View\Composers\Project\StaffComposer;
use App\Http\View\Composers\Project\ClientsCompaniesListComposer;
use App\Http\View\Composers\Project\ManufacturersListComposer;
use App\Http\View\Composers\Project\NewsComposer;
use App\Http\View\Composers\Project\PromotionsSliderComposer;
use App\Http\View\Composers\Project\ToolsCategoriesWithToolsComposer;
use App\Http\View\Composers\Project\VendorsComposer;
use App\Http\View\Composers\Project\WorktimeComposer;
use App\Http\View\Composers\Project\WorktimeFilialTodayComposer;
use App\Http\View\Composers\Project\PluginsComposer;

use App\Http\View\Composers\Project\WorktimeTodayComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class ComposerProjectServiceProvider extends ServiceProvider
{

    public function boot()
    {

        if (Schema::hasTable('domains')) {

            $domain = Domain::with([
                'site:id,alias'
            ])
                ->where('domain', request()->getHost())
                ->first([
                    'domain',
                    'site_id'
                ]);
//            dd($domain);

            if ($domain) {
                $alias = $domain->site->alias;

//                view()->composer([
//                    $alias . '.layouts.headers.header',
//                    $alias . '.layouts.footers.footer',
//                ], DepartmentsComposer::class);

                view()->composer([
                    $alias . '.layouts.navigations.nav',
                    'project.includes.menus.menu'
                ], NavigationsComposer::class);

                view()->composer([
                    'project.includes.catalogs_services.accordion',
                    'project.includes.catalogs_services.menu_one_level',
                    'project.includes.catalogs_services.sidebar',
                ], CatalogsServiceComposer::class);

                view()->composer([
                    'project.includes.catalogs_goods.accordion',
                    'project.includes.catalogs_goods.menu',
                    'project.includes.catalogs_goods.images_menu',
                    $alias . '.layouts.navigations.nav_catalogs_goods',
                ], CatalogsGoodsComposer::class);

                view()->composer([
                    'project.includes.news.images'
                ], NewsComposer::class);



                view()->composer([
                    'project.includes.staff.section',
                    'project.includes.staff.list',
                ], StaffComposer::class);
                view()->composer('project.includes.schedules.worktime_filial_today', WorktimeFilialTodayComposer::class);
                view()->composer('project.includes.worktimes.today', WorktimeTodayComposer::class);
                view()->composer('project.includes.plugins.list', PluginsComposer::class);

                view()->composer('project.includes.catalogs_goods.filters.price', PricesGoodsPriceFilterComposer::class);
                view()->composer('project.includes.catalogs_goods.filters.weight', PricesGoodsWeightFilterComposer::class);
                view()->composer('project.includes.catalogs_goods.filters.raws_articles_groups', PricesGoodsRawsArticlesGroupsFilterComposer::class);
                view()->composer('project.includes.catalogs_goods.filters.catalogs_goods_items', CatalogsGoodsItemsFilterComposer::class);

                view()->composer([
                    'project.includes.clients.companies_list',
                    'project.includes.clients.section'
                ], ClientsCompaniesListComposer::class);

                view()->composer('project.includes.promotions.slider', PromotionsSliderComposer::class);

                view()->composer([
                    'project.includes.manufacturers.list',
                    'project.includes.manufacturers.section'
                ], ManufacturersListComposer::class);

                view()->composer([
                    'project.includes.vendors.section'
                ], VendorsComposer::class);

//                view()->composer($alias. '.layouts.headers.includes.cart', CartComposer::class);
                view()->composer($alias. '.pages.contacts.index', FilialComposer::class);

                view()->composer([
                    $alias . '.pages.catalogs_goods.index'
                ], DisplayModesComposer::class);

                view()->composer('project.includes.tools_categories.sidebar_with_items', ToolsCategoriesWithToolsComposer::class);

                view()->composer('project.includes.prices_services.providers', ProvidersComposer::class);

            }
        }
    }

    public function register()
    {
        //
    }
}
