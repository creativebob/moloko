<?php

namespace App\Providers;

use App\User;
use App\RightsRole;
use App\Company;
use App\Role;
use App\Place;
use App\PlacesType;

use App\Right;
use App\Entity;
use App\Region;
use App\Area;
use App\City;
use App\Department;
use App\Employee;

use App\Site;
use App\Page;
use App\Navigation;
use App\Menu;
use App\News;

use App\Position;

use App\Staffer;
use App\Booklist;
use App\Sector;

use App\Album;
use App\AlbumsCategory;
use App\AlbumsSetting;

use App\Photo;
use App\Folder;

use App\Service;
use App\ServicesCategory;
use App\ServicesProduct;

// use App\Article;

use App\Policies\UserPolicy;
use App\Policies\RightsRolePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\RolePolicy;
use App\Policies\PlacePolicy;
use App\Policies\PlacesTypePolicy;

use App\Policies\RightPolicy;
use App\Policies\EntityPolicy;

use App\Policies\RegionPolicy;
use App\Policies\AreaPolicy;
use App\Policies\CityPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\EmployeePolicy;

use App\Policies\SitePolicy;
use App\Policies\PagePolicy;
use App\Policies\NavigationPolicy;
use App\Policies\MenuPolicy;
use App\Policies\NewsPolicy;


use App\Policies\PositionPolicy;
use App\Policies\StafferPolicy;
use App\Policies\BooklistPolicy;
use App\Policies\SectorPolicy;

use App\Policies\AlbumPolicy;
use App\Policies\AlbumsCategoryPolicy;
use App\Policies\AlbumsSettingPolicy;

use App\Policies\PhotoPolicy;
use App\Policies\FolderPolicy;

use App\Policies\ServicePolicy;
use App\Policies\ServicesCategoryPolicy;
use App\Policies\ServicesProductPolicy;

// use App\Policies\ArticlePolicy;


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

        User::class => UserPolicy::class, 
        RightsRole::class => RightsRolePolicy::class, 
        Company::class => CompanyPolicy::class, 
        Right::class => RightPolicy::class, 
        Entity::class => EntityPolicy::class, 
        Role::class => RolePolicy::class,
        Place::class => PlacePolicy::class,
        PlacesType::class => PlacesTypePolicy::class,
        Position::class => PositionPolicy::class,
        Region::class => RegionPolicy::class,
        Area::class => AreaPolicy::class,
        City::class => CityPolicy::class,
        Department::class => DepartmentPolicy::class,
        Employee::class => EmployeePolicy::class,
        Menu::class => MenuPolicy::class,
        Navigation::class => NavigationPolicy::class,
        Page::class => PagePolicy::class,
        Site::class => SitePolicy::class,
        News::class => NewsPolicy::class,
        Staffer::class => StafferPolicy::class,
        Booklist::class => BooklistPolicy::class,
        Sector::class => SectorPolicy::class,
        Album::class => AlbumPolicy::class,
        AlbumsCategory::class => AlbumsCategoryPolicy::class,
        AlbumsSetting::class => AlbumsSettingPolicy::class,
        Photo::class => PhotoPolicy::class,
        Folder::class => FolderPolicy::class,
        Service::class => ServicePolicy::class,
        ServicesCategory::class => ServicesCategoryPolicy::class,
        ServicesProduct::class => ServicesProductPolicy::class,
    ];

    public function boot()
    {
      $this->registerPolicies();
    }

}
