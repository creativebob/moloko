<?php

namespace App\Providers;

use App\User;
use App\RightsRole;
use App\Company;
use App\ExtraRequisite;

use App\Supplier;
use App\Application;

use App\Manufacturer;
use App\Dealer;
use App\Client;
use App\Bank;
use App\BankAccount;

use App\Role;
// use App\Place;

// use App\PlacesType;

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

use App\Rubricator;
use App\RubricatorsItem;
use App\News;

use App\Account;
use App\Post;
use App\SocialNetwork;
use App\Campaign;

use App\Plan;

use App\Salary;

use App\Position;

use App\Staffer;
use App\Booklist;
use App\Sector;

use App\Album;
use App\AlbumsCategory;
use App\PhotoSetting;

use App\Photo;

// Услуги
use App\ServicesCategory;
use App\Policies\ServicesCategoryPolicy;
use App\Service;
use App\Policies\ServicePolicy;

use App\PricesService;
use App\Policies\PricesServicePolicy;

// Рабочие процессы
use App\WorkflowsCategory;
use App\Policies\WorkflowsCategoryPolicy;
use App\Workflow;
use App\Policies\WorkflowPolicy;

// use App\ServicesArticle;
// use App\Policies\ServicesArticlePolicy;
// use App\ServicesProduct;
// use App\Policies\ServicesProductPolicy;


// Расходные материалы
use App\ExpendablesCategory;
use App\Policies\ExpendablesCategoryPolicy;


// Товары
use App\GoodsCategory;
use App\Policies\GoodsCategoryPolicy;
use App\Goods;
use App\Policies\GoodsPolicy;

// use App\GoodsProduct;
// use App\Policies\GoodsProductPolicy;

// Сырье
use App\RawsCategory;
use App\Policies\RawsCategoryPolicy;
use App\Raw;
use App\Policies\RawPolicy;

// use App\RawsProduct;
// use App\Policies\RawsProductPolicy;

// Оборудование
use App\EquipmentsCategory;
use App\Policies\EquipmentsCategoryPolicy;
use App\Equipment;
use App\Policies\EquipmentPolicy;





use App\Lead;
use App\Note;
use App\Challenge;
use App\Claim;

use App\Stage;
use App\Field;
use App\Rule;

use App\Feedback;


use App\Policies\UserPolicy;
use App\Policies\RightsRolePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ExtraRequisitePolicy;

use App\Policies\SupplierPolicy;
use App\Policies\ApplicationPolicy;

use App\Policies\ManufacturerPolicy;
use App\Policies\DealerPolicy;
use App\Policies\ClientPolicy;
use App\Policies\BankPolicy;
use App\Policies\BankAccountPolicy;

use App\Policies\RolePolicy;
// use App\Policies\PlacePolicy;

// use App\Policies\PlacesTypePolicy;

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

use App\Policies\RubricatorPolicy;
use App\Policies\RubricatorsItemPolicy;
use App\Policies\NewsPolicy;

use App\Policies\AccountPolicy;
use App\Policies\PostPolicy;
use App\Policies\SocialNetworkPolicy;
use App\Policies\CampaignPolicy;

use App\Policies\PlanPolicy;
use App\Policies\SalaryPolicy;

use App\Policies\PositionPolicy;
use App\Policies\StafferPolicy;
use App\Policies\BooklistPolicy;
use App\Policies\SectorPolicy;

use App\Policies\AlbumPolicy;
use App\Policies\AlbumsCategoryPolicy;
use App\Policies\PhotoSettingPolicy;

use App\Policies\PhotoPolicy;





use App\Policies\LeadPolicy;
use App\Policies\NotePolicy;
use App\Policies\ChallengePolicy;
use App\Policies\ClaimPolicy;

use App\Policies\StagePolicy;
// use App\Policies\FieldPolicy;
// use App\Policies\RulePolicy;

use App\Policies\FeedbackPolicy;

use App\RoomsCategory;
use App\Policies\RoomsCategoryPolicy;
use App\Room;
use App\Policies\RoomPolicy;

use App\Stock;
use App\Policies\StockPolicy;

use App\CatalogsGoods;
use App\Policies\CatalogsGoodsPolicy;
use App\CatalogsGoodsItem;
use App\Policies\CatalogsGoodsItemPolicy;

use App\CatalogsService;
use App\Policies\CatalogsServicePolicy;
use App\CatalogsServiceItem;
use App\Policies\CatalogsServiceItemPolicy;

use App\Estimate;
use App\Policies\EstimatePolicy;
// use App\EstimatesItem;
// use App\Policies\EstimatesItemPolicy;



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


        // Артикулы
        'App\ArticlesGroup' => 'App\Policies\ArticlesGroupPolicy',
        'App\ProcessesGroup' => 'App\Policies\ProcessesGroupPolicy',

        // Расчеты и заказы
        'App\Order' => 'App\Policies\OrderPolicy',
        Estimate::class => EstimatePolicy::class,
        // EstimatesItem::class => EstimatesItemPolicy::class,


        // Показатели
        'App\Indicator' => 'App\Policies\IndicatorPolicy',

        // Каталоги
        CatalogGoods::class => CatalogGoodsPolicy::class,
        CatalogGoodsItem::class => CatalogGoodsItemPolicy::class,

        CatalogService::class => CatalogServicePolicy::class,
        CatalogServiceItem::class => CatalogServiceItemPolicy::class,


        User::class => UserPolicy::class,
        RightsRole::class => RightsRolePolicy::class,
        Company::class => CompanyPolicy::class,
        ExtraRequisite::class => ExtraRequisitePolicy::class,

        Supplier::class => SupplierPolicy::class,
        Application::class => ApplicationPolicy::class,
        'App\Consignment' => 'App\Policies\ConsignmentPolicy',

        Manufacturer::class => ManufacturerPolicy::class,
        Dealer::class => DealerPolicy::class,
        Client::class => ClientPolicy::class,
        Bank::class => BankPolicy::class,
        BankAccount::class => BankAccountPolicy::class,

        Right::class => RightPolicy::class,
        Entity::class => EntityPolicy::class,
        Role::class => RolePolicy::class,


        // Place::class => PlacePolicy::class,
        // PlacesType::class => PlacesTypePolicy::class,

        Account::class => AccountPolicy::class,
        Post::class => PostPolicy::class,
        SocialNetwork::class => SocialNetworkPolicy::class,
        Campaign::class => CampaignPolicy::class,

        Plan::class => PlanPolicy::class,
        Salary::class => SalaryPolicy::class,

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



        Rubricator::class => RubricatorPolicy::class,
        RubricatorsItem::class => RubricatorsItemPolicy::class,
        News::class => NewsPolicy::class,

        Staffer::class => StafferPolicy::class,
        Booklist::class => BooklistPolicy::class,
        Sector::class => SectorPolicy::class,
        Album::class => AlbumPolicy::class,
        AlbumsCategory::class => AlbumsCategoryPolicy::class,
        PhotoSetting::class => PhotoSettingPolicy::class,
        Photo::class => PhotoPolicy::class,

        // Услуги
        Service::class => ServicePolicy::class,
        ServicesCategory::class => ServicesCategoryPolicy::class,

        PricesService::class => PricesServicePolicy::class,

        // Рабочие процессы
        WorkflowsCategory::class => WorkflowsCategoryPolicy::class,
        Workflow::class => WorkflowPolicy::class,



        // Товары
        Goods::class => GoodsPolicy::class,
        GoodsCategory::class => GoodsCategoryPolicy::class,

        // Сырье
        Raw::class => RawPolicy::class,
        RawsCategory::class => RawsCategoryPolicy::class,

        // Оборудование
        Equipment::class => EquipmentPolicy::class,
        EquipmentsCategory::class => EquipmentsCategoryPolicy::class,

        // Помещения
        RoomCategory::class => RoomCategoryPolicy::class,
        Room::class => RoomPolicy::class,

        // Расходные материалы
        ExpendablesCategory::class => ExpendablesCategoryPolicy::class,

        Stock::class => StockPolicy::class,

        Lead::class => LeadPolicy::class,
        Note::class => NotePolicy::class,
        Challenge::class => ChallengePolicy::class,
        Claim::class => ClaimPolicy::class,
        Stage::class => StagePolicy::class,
        Feedback::class => FeedbackPolicy::class,

    ];

    public function boot()
    {
      $this->registerPolicies();
    }

}
