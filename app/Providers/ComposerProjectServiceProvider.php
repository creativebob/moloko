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
            'project.includes.menus.menu'
        ], NavigationsComposer::class);

        view()->composer([
            'project.includes.catalogs_services.accordion',
            'project.includes.catalogs_services.menu_one_level',
            ], CatalogsServiceComposer::class);

        view()->composer([
            'project.includes.catalogs_goods.accordion', 
            'project.includes.catalogs_goods.menu',
            $host. '.layouts.navigations.nav_catalogs_goods', 
        ], CatalogsGoodsComposer::class);

        view()->composer('project.includes.staff.list', StaffComposer::class);

    }

    public function register()
    {
        //
    }
}
