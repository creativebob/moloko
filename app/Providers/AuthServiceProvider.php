<?php

namespace App\Providers;

use App\User;
use App\Page;
use App\RightsRole;
use App\Company;
use App\Right;
use App\Entity;

use App\Policies\UserPolicy;
use App\Policies\PagePolicy;
use App\Policies\RightsRolePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\RightPolicy;
use App\Policies\EntityPolicy;

use Illuminate\Support\Facades\Gate as GateContract;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy', 
        // Page::class => PagePolicy::class,
        User::class => UserPolicy::class, 
        RightsRole::class => RightsRolePolicy::class, 
        Company::class => CompanyPolicy::class, 
        Right::class => RightPolicy::class, 
        Entity::class => EntityPolicy::class, 
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */

    // public function boot()
    // {

    //     $this->registerPolicies();

    //     Gate::define('index-user', function ($user, $access) {
    //     return  $result = $access->where(['right_action' => 'view-user'])->count() == 1;

    //     });

    // }

    public function boot()
    {
      $this->registerPolicies();
    }

}
