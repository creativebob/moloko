<?php

namespace App\Providers\Project;

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
//        Relation::morphMap([
//            'App\Lead' => \App\Models\Project\Lead::class,
//            'App\User' => \App\Models\Project\User::class,
//            'App\Company' => \App\Models\Project\Company::class,
//        ]);
    }
}
