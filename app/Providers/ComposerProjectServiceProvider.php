<?php

namespace App\Providers;

use App\Http\ViewComposers\Project\DepartmentsComposer;
use App\Http\ViewComposers\Project\CatalogsServiceComposer;
use App\Http\ViewComposers\Project\CatalogsGoodsComposer;
use App\Http\ViewComposers\Project\NavigationsComposer;
use App\Http\ViewComposers\Project\StaffComposer;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;



class ComposerProjectServiceProvider extends ServiceProvider
{

    public function boot()
    {
    	$host = request()->getHost();
        view()->composer([
        	$host. '.layouts.headers.header', 
        	$host. '.layouts.footers.footer', 
        ], DepartmentsComposer::class);

        view()->composer([
            $host. '.layouts.navigations.nav', 
            $host. '.includes.menus.menu'
        ], NavigationsComposer::class);

        view()->composer([
            $host. '.includes.catalogs_services.accordion',
            $host. '.includes.catalogs_services.menu_one_level',
            ], CatalogsServiceComposer::class);

        view()->composer($host. '.includes.catalogs_goods.accordion', CatalogsGoodsComposer::class);
        view()->composer($host. '.includes.staff.list', StaffComposer::class);

    }

    public function register()
    {
        //
    }
}
