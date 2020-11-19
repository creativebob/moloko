<?php

namespace App\Providers\System\View;

use App\Http\View\Composers\System\Widgets\ClientsIndicatorsComposer;
use App\Http\View\Composers\System\Widgets\SalesDepartmentBurdenComposer;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('system.pages.dashboard.widgets.clients_indicators', ClientsIndicatorsComposer::class);
        view()->composer('system.pages.dashboard.widgets.sales_department_burden', SalesDepartmentBurdenComposer::class);
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
