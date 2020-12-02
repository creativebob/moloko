<?php

namespace App\Providers\System;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers\System';

    protected $oldNamespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        $this->mapUpdateRoutes();
        $this->mapParsersRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->namespace($this->oldNamespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->oldNamespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Роуты обновлений системы
     *
     * @return void
     */
    protected function mapUpdateRoutes()
    {
        Route::prefix('admin/updates')
            ->middleware('web')
            ->namespace($this->namespace)
            ->name('updates.')
            ->group(base_path('routes/system/updates.php'));
    }

    /**
     * Роуты парсеров системы
     *
     * @return void
     */
    protected function mapParsersRoutes()
    {
        Route::prefix('admin/parsers')
            ->middleware('web')
            ->namespace($this->namespace)
            ->name('parsers.')
            ->group(base_path('routes/system/parsers.php'));
    }
}
