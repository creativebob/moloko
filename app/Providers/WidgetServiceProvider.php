<?php

namespace App\Providers;

use App\Http\View\Composers\System\Widgets\ClientsIndicatorsComposer;
use App\Http\View\Composers\System\Widgets\SalesDepartmentBurdenComposer;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{

    public function boot()
    {

        view()->composer('system.pages.dashboard.widgets.clients_indicators', ClientsIndicatorsComposer::class);
        view()->composer('system.pages.dashboard.widgets.sales_department_burden', SalesDepartmentBurdenComposer::class);

    }

    public function register()
    {
        //
    }
}
