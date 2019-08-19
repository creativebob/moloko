<?php

namespace App\Providers;

use App\Http\ViewComposers\Project\DepartmentsComposer;
use App\Http\ViewComposers\Project\CatalogsServiceComposer;
use App\Http\ViewComposers\Project\CatalogsGoodsComposer;
use App\Http\ViewComposers\Project\NavigationsComposer;
use App\Http\ViewComposers\Project\StaffComposer;

use App\Site;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;



class ComposerProjectServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $domain = request()->getHost();

        $site = Site::where('domain', $domain)
            ->first([
                'alias'
            ]);

        if(!is_null($site)) {
            $alias = $site->alias;

            view()->composer([
                $alias. '.layouts.headers.header',
                $alias. '.layouts.footers.footer',
            ], DepartmentsComposer::class);

            view()->composer([
                $alias. '.layouts.navigations.nav',
                'project.includes.menus.menu'
            ], NavigationsComposer::class);

            view()->composer([
                'project.includes.catalogs_services.accordion',
                'project.includes.catalogs_services.menu_one_level',
            ], CatalogsServiceComposer::class);

            view()->composer([
                'project.includes.catalogs_goods.accordion',
                'project.includes.catalogs_goods.menu',
                $alias. '.layouts.navigations.nav_catalogs_goods',
            ], CatalogsGoodsComposer::class);

            view()->composer('project.includes.staff.list', StaffComposer::class);
        }

    }

    public function register()
    {
        //
    }
}
