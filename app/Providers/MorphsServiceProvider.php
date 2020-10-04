<?php

namespace App\Providers;

use App\Goods;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
//            'User' => \App\User::class,
//            'Company' => \App\Company::class,
//            'Department' => \App\Department::class,
//            'Lead' => \App\Lead::class,
        ]);
    }
}
