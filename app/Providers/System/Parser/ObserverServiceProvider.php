<?php

namespace App\Providers\System\Parser;

use App\Models\System\Parser\EstimatesGoodsItem;
use App\Observers\Project\EstimatesGoodsItemObserver;
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
