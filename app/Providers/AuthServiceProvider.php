<?php

namespace App\Providers;

use App\User;
use App\RightsRole;
use App\Company;
use App\ExtraRequisite;
use App\Supplier;
use App\Manufacturer;
use App\Dealer;
use App\Client;
use App\Bank;

use App\Role;
use App\Place;
use App\Stock;
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
use App\Catalog;
use App\CatalogProduct;

use App\Account;
use App\Post;

use App\Position;

use App\Staffer;
use App\Booklist;
use App\Sector;

use App\Album;
use App\AlbumsCategory;
use App\EntitySetting;

use App\Photo;
use App\Folder;

use App\ServicesArticle;
use App\ServicesCategory;
use App\ServicesProduct;
use App\Service;

use App\Goods;
use App\GoodsCategory;
use App\GoodsProduct;

use App\Raw;
use App\RawsCategory;
use App\RawsProduct;

use App\Lead;
use App\Note;
use App\Challenge;
use App\Claim;

use App\Stage;
use App\Field;
use App\Rule;

use App\Feedback;

use App\Order;

use App\Policies\UserPolicy;
use App\Policies\RightsRolePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ExtraRequisitePolicy;

use App\Policies\SupplierPolicy;
use App\Policies\ManufacturerPolicy;
use App\Policies\DealerPolicy;
use App\Policies\ClientPolicy;
use App\Policies\BankPolicy;

use App\Policies\RolePolicy;
use App\Policies\PlacePolicy;
use App\Policies\StockPolicy;
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
use App\Policies\CatalogPolicy;
use App\Policies\CatalogProductPolicy;

use App\Policies\AccountPolicy;
use App\Policies\PostPolicy;

use App\Policies\PositionPolicy;
use App\Policies\StafferPolicy;
use App\Policies\BooklistPolicy;
use App\Policies\SectorPolicy;

use App\Policies\AlbumPolicy;
use App\Policies\AlbumsCategoryPolicy;
use App\Policies\EntitySettingPolicy;

use App\Policies\PhotoPolicy;
use App\Policies\FolderPolicy;

use App\Policies\ServicePolicy;
use App\Policies\ServicesCategoryPolicy;
use App\Policies\ServicesProductPolicy;
use App\Policies\ServicesArticlePolicy;

use App\Policies\GoodsPolicy;
use App\Policies\GoodsCategoryPolicy;
use App\Policies\GoodsProductPolicy;

use App\Policies\RawPolicy;
use App\Policies\RawsCategoryPolicy;
use App\Policies\RawsProductPolicy;

use App\Policies\LeadPolicy;
use App\Policies\NotePolicy;
use App\Policies\ChallengePolicy;
use App\Policies\ClaimPolicy;

use App\Policies\StagePolicy;
// use App\Policies\FieldPolicy;
// use App\Policies\RulePolicy;

use App\Policies\FeedbackPolicy;

use App\Policies\OrderPolicy;


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
        ExtraRequisite::class => ExtraRequisitePolicy::class, 

        Supplier::class => SupplierPolicy::class, 
        Manufacturer::class => ManufacturerPolicy::class, 
        Dealer::class => DealerPolicy::class, 
        Client::class => ClientPolicy::class,
        Bank::class => BankPolicy::class,
        Right::class => RightPolicy::class, 
        Entity::class => EntityPolicy::class, 
        Role::class => RolePolicy::class,
        Place::class => PlacePolicy::class,
        Stock::class => StockPolicy::class,
        PlacesType::class => PlacesTypePolicy::class,

        Account::class => AccountPolicy::class,
        Post::class => PostPolicy::class,

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
        Catalog::class => CatalogPolicy::class,
        CatalogProduct::class => CatalogProductPolicy::class,
        Staffer::class => StafferPolicy::class,
        Booklist::class => BooklistPolicy::class,
        Sector::class => SectorPolicy::class,
        Album::class => AlbumPolicy::class,
        AlbumsCategory::class => AlbumsCategoryPolicy::class,
        EntitySetting::class => EntitySettingPolicy::class,
        Photo::class => PhotoPolicy::class,
        Folder::class => FolderPolicy::class,
        Service::class => ServicePolicy::class,
        ServicesCategory::class => ServicesCategoryPolicy::class,
        ServicesProduct::class => ServicesProductPolicy::class,
        ServicesArticle::class => ServicesArticlePolicy::class,
        Goods::class => GoodsPolicy::class,
        GoodsCategory::class => GoodsCategoryPolicy::class,
        GoodsProduct::class => GoodsProductPolicy::class,
        Raw::class => RawPolicy::class,
        RawsCategory::class => RawsCategoryPolicy::class,
        RawsProduct::class => RawsProductPolicy::class,
        Lead::class => LeadPolicy::class,
        Note::class => NotePolicy::class,
        Challenge::class => ChallengePolicy::class,
        Claim::class => ClaimPolicy::class,
        Stage::class => StagePolicy::class,
        Feedback::class => FeedbackPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    public function boot()
    {
      $this->registerPolicies();
    }

}
