<?php

namespace App\Providers;

use App\Models\Project\EstimatesGoodsItem;
use App\Observers\Project\EstimatesGoodsItemObserver;
use Illuminate\Support\ServiceProvider;

class ObserverProjectServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        EstimatesGoodsItem::observe(EstimatesGoodsItemObserver::class);
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
