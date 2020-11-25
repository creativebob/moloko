<?php

namespace App\Providers\System;

use App\Models\System\Documents\Consignment;
use App\Models\System\Documents\ConsignmentsItem;
use App\Models\System\Documents\Production;
use App\Models\System\Documents\ProductionsItem;
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
//            'Consignment' => Consignment::class,
//            'ConsignmentsItem' => ConsignmentsItem::class,
//            'Production' => Production::class,
//            'ProductionsItem' => ProductionsItem::class,

//            'User' => \App\User::class,
//            'Company' => \App\Company::class,
//            'Department' => \App\Department::class,
//            'Lead' => \App\Lead::class,
        ]);
    }
}
