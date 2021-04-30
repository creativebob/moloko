<?php

namespace App\Providers\Project;

use App\Models\Project\EstimatesGoodsItem;
use App\Models\Project\EstimatesServicesItem;
use App\Observers\Project\EstimatesGoodsItemObserver;
use App\Observers\Project\EstimatesServicesItemObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        EstimatesGoodsItem::observe(EstimatesGoodsItemObserver::class);
        EstimatesServicesItem::observe(EstimatesServicesItemObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
