<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Schema::defaultStringLength(191);

        // Проверки If Else на шаблонах

        // Display
        Blade::if('display', function ($item) {
            $result = $item->display == 1;
            return $result;
        });

        // Moderation
        Blade::if('moderation', function ($item) {
            $result = $item->moderation == 1;
            return $result;
        });

        // Шаблон
        Blade::if('template', function ($item) {
            $result = is_null($item->company_id) && is_null($item->system);
            return $result;
        });

    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('path.public', function() {
            return base_path().'/public_html';
        });
    }
}
