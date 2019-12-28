<?php

namespace App\Providers;

use App\Domain;
use App\Http\ViewComposers\Project\CartComposer;
use App\Http\ViewComposers\Project\CatalogsGoodsItemsFilterComposer;
use App\Http\ViewComposers\Project\DepartmentsComposer;
use App\Http\ViewComposers\Project\CatalogsServiceComposer;
use App\Http\ViewComposers\Project\CatalogsGoodsComposer;
use App\Http\ViewComposers\Project\DisplayModesComposer;
use App\Http\ViewComposers\Project\FilialComposer;
use App\Http\ViewComposers\Project\NavigationsComposer;
use App\Http\ViewComposers\Project\PricesGoodsPriceFilterComposer;
use App\Http\ViewComposers\Project\PricesGoodsRawsArticlesGroupsFilterComposer;
use App\Http\ViewComposers\Project\PricesGoodsWeightFilterComposer;
use App\Http\ViewComposers\Project\StaffComposer;
use App\Http\ViewComposers\Project\ClientsCompaniesListComposer;
use App\Http\ViewComposers\Project\ManufacturersListComposer;
use App\Http\ViewComposers\Project\NewsComposer;
use App\Http\ViewComposers\Project\PromotionsSliderComposer;
use App\Http\ViewComposers\Project\WorktimeFilialTodayComposer;

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



                view()->composer('project.includes.staff.list', StaffComposer::class);
                view()->composer('project.includes.schedules.worktime_filial_today', WorktimeFilialTodayComposer::class);

                view()->composer('project.includes.catalogs_goods.filters.price', PricesGoodsPriceFilterComposer::class);
                view()->composer('project.includes.catalogs_goods.filters.weight', PricesGoodsWeightFilterComposer::class);
                view()->composer('project.includes.catalogs_goods.filters.raws_articles_groups', PricesGoodsRawsArticlesGroupsFilterComposer::class);
                view()->composer('project.includes.catalogs_goods.filters.catalogs_goods_items', CatalogsGoodsItemsFilterComposer::class);

                view()->composer('project.includes.clients.companies_list', ClientsCompaniesListComposer::class);

                view()->composer('project.includes.promotions.slider', PromotionsSliderComposer::class);

                view()->composer('project.includes.manufacturers.list', ManufacturersListComposer::class);

//                view()->composer($alias. '.layouts.headers.includes.cart', CartComposer::class);
                view()->composer($alias. '.pages.contacts.index', FilialComposer::class);

                view()->composer([
                    $alias . '.pages.catalogs_goods.index'
                ], DisplayModesComposer::class);
            }
        }
    }

    public function register()
    {
        //
    }
}
