<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Артикулы
use App\Article;
use App\Observers\ArticleObserver;

use App\Goods;
use App\Observers\GoodsObserver;
use App\Raw;
use App\Observers\RawObserver;

use App\Room;
use App\Observers\RoomObserver;

// Процессы
use App\Process;
use App\Observers\ProcessObserver;

use App\Service;
use App\Observers\ServiceObserver;
use App\Workflow;
use App\Observers\WorkflowObserver;


use App\Stock;
use App\Observers\StockObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // Артикулы
        Article::observe(ArticleObserver::class);
        Goods::observe(GoodsObserver::class);
        Raw::observe(RawObserver::class);
        Room::observe(RoomObserver::class);

        // Процессы
        Process::observe(ProcessObserver::class);
        Service::observe(ServiceObserver::class);
        Workflow::observe(WorkflowObserver::class);


        Stock::observe(StockObserver::class);
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
