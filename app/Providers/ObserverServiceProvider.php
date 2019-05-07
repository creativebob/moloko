<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Article;
use App\Observers\ArticleObserver;

use App\Process;
use App\Observers\ProcessObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Article::observe(ArticleObserver::class);

        Process::observe(ProcessObserver::class);
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
