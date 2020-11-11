<?php

namespace App\Providers;

use App\ArticlesGroup;
use App\Attachment;
use App\AttachmentsCategory;
use App\BusinessCase;
use App\Container;
use App\ContainersCategory;
use App\Mailing;
use App\MailingList;
use App\Models\System\Documents\Consignment;
use App\Models\System\Documents\Production;
use App\Models\System\Stocks\AttachmentsStock;
use App\Models\System\Stocks\ContainersStock;
use App\Discount;
use App\Dispatch;
use App\Domain;
use App\Models\System\Stocks\GoodsStock;
use App\Indicator;
use App\Metric;
use App\Order;
use App\Outcome;
use App\OutcomesCategory;
use App\Outlet;
use App\Policies\ArticlesGroupPolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\AttachmentsCategoryPolicy;
use App\Policies\BusinessCasePolicy;
use App\Policies\ContainerPolicy;
use App\Policies\ContainersCategoryPolicy;
use App\Policies\DiscountPolicy;
use App\Policies\DispatchPolicy;
use App\Policies\Documents\ConsignmentPolicy;
use App\Policies\Documents\ProductionPolicy;
use App\Policies\DomainPolicy;
use App\Policies\IndicatorPolicy;
use App\Policies\MailingListPolicy;
use App\Policies\MailingPolicy;
use App\Policies\MetricPolicy;
use App\Policies\OrderPolicy;
use App\Policies\OutcomePolicy;
use App\Policies\OutcomesCategoryPolicy;
use App\Policies\OutletPolicy;
use App\Policies\PortfolioPolicy;
use App\Policies\PortfoliosItemPolicy;
use App\Policies\PricesGoodsPolicy;
use App\Policies\ProcessesGroupPolicy;
use App\Policies\PromotionPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\CompaniesSettingPolicy;
use App\Policies\Stocks\AttachmentsStockPolicy;
use App\Policies\Stocks\ContainersStockPolicy;
use App\Policies\Stocks\GoodsStockPolicy;
use App\Policies\Stocks\RawsStockPolicy;
use App\Policies\SubscriberPolicy;
use App\Policies\TemplatePolicy;
use App\Policies\TemplatesCategoryPolicy;
use App\Policies\ToolsStockPolicy;
use App\Policies\VendorPolicy;
use App\Portfolio;
use App\PortfoliosItem;
use App\PricesGoods;
use App\ProcessesGroup;
use App\Promotion;
use App\Models\System\Stocks\RawsStock;
use App\Schedule;
use App\CompaniesSetting;
use App\Models\System\Stocks\ToolsStock;
use App\Subscriber;
use App\Template;
use App\TemplatesCategory;
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
use App\ServicesCategory;
use App\Policies\ServicesCategoryPolicy;
use App\Service;
use App\Policies\ServicePolicy;
use App\PricesService;
use App\Policies\PricesServicePolicy;
use App\Vendor;
use App\WorkflowsCategory;
use App\Policies\WorkflowsCategoryPolicy;
use App\Workflow;
use App\Policies\WorkflowPolicy;
use App\ExpendablesCategory;
use App\Policies\ExpendablesCategoryPolicy;
use App\GoodsCategory;
use App\Policies\GoodsCategoryPolicy;
use App\Goods;
use App\Policies\GoodsPolicy;
use App\RawsCategory;
use App\Policies\RawsCategoryPolicy;
use App\Raw;
use App\Policies\RawPolicy;
use App\ToolsCategory;
use App\Policies\ToolsCategoryPolicy;
use App\Tool;
use App\Policies\ToolPolicy;
use App\Lead;
use App\Note;
use App\Challenge;
use App\Claim;
use App\Stage;
use App\Feedback;
use App\Policies\UserPolicy;
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
use App\Policies\CampaignPolicy;
use App\Policies\PlanPolicy;
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
use App\CatalogsServicesItem;
use App\Policies\CatalogsServicesItemPolicy;
use App\Models\System\Documents\Estimate;
use App\Policies\Documents\EstimatePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
//        'App\Model' => 'App\Policies\ModelPolicy',

        CompaniesSetting::class => CompaniesSettingPolicy::class,

        // Артикулы
        ArticlesGroup::class => ArticlesGroupPolicy::class,
        ProcessesGroup::class => ProcessesGroupPolicy::class,

        // Расчеты и заказы
        Order::class => OrderPolicy::class,
        Estimate::class => EstimatePolicy::class,

        // Показатели
        Indicator::class => IndicatorPolicy::class,

        // Каталоги
        CatalogsGoods::class => CatalogsGoodsPolicy::class,
        CatalogsGoodsItem::class => CatalogsGoodsItemPolicy::class,

        CatalogsService::class => CatalogsServicePolicy::class,
        CatalogsServicesItem::class => CatalogsServicesItemPolicy::class,


        User::class => UserPolicy::class,
//        RightsRole::class => RightsRolePolicy::class,
        Company::class => CompanyPolicy::class,
        ExtraRequisite::class => ExtraRequisitePolicy::class,

        Supplier::class => SupplierPolicy::class,
        Application::class => ApplicationPolicy::class,

        Manufacturer::class => ManufacturerPolicy::class,
        Vendor::class => VendorPolicy::class,
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
//        SocialNetwork::class => SocialNetworkPolicy::class,
        Campaign::class => CampaignPolicy::class,

        Plan::class => PlanPolicy::class,
//        Salary::class => SalaryPolicy::class,

        Position::class => PositionPolicy::class,
        Region::class => RegionPolicy::class,
        Area::class => AreaPolicy::class,
        City::class => CityPolicy::class,
        Department::class => DepartmentPolicy::class,
        Employee::class => EmployeePolicy::class,
        Menu::class => MenuPolicy::class,
        Navigation::class => NavigationPolicy::class,
        Page::class => PagePolicy::class,
        Domain::class => DomainPolicy::class,
        Site::class => SitePolicy::class,
        Promotion::class => PromotionPolicy::class,
        Dispatch::class => DispatchPolicy::class,
        Schedule::class => SchedulePolicy::class,

        Metric::class => MetricPolicy::class,

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
        GoodsStock::class => GoodsStockPolicy::class,
        PricesGoods::class => PricesGoodsPolicy::class,

        // Сырье
        Raw::class => RawPolicy::class,
        RawsCategory::class => RawsCategoryPolicy::class,
        RawsStock::class => RawsStockPolicy::class,

        // Упаковка
        Container::class => ContainerPolicy::class,
        ContainersCategory::class => ContainersCategoryPolicy::class,
        ContainersStock::class => ContainersStockPolicy::class,

        // Вложения
        Attachment::class => AttachmentPolicy::class,
        AttachmentsCategory::class => AttachmentsCategoryPolicy::class,
        AttachmentsStock::class => AttachmentsStockPolicy::class,

        // Инструменты
        Tool::class => ToolPolicy::class,
        ToolsCategory::class => ToolsCategoryPolicy::class,
        ToolsStock::class => ToolsStockPolicy::class,

        // Помещения
        RoomsCategory::class => RoomsCategoryPolicy::class,
        Room::class => RoomPolicy::class,

        // Расходные материалы
        ExpendablesCategory::class => ExpendablesCategoryPolicy::class,

        // Скидки
        Discount::class => DiscountPolicy::class,

        // Товарные накладные
        Consignment::class => ConsignmentPolicy::class,

        // Наряды на производство
        Production::class => ProductionPolicy::class,

        Stock::class => StockPolicy::class,

        Outlet::class => OutletPolicy::class,

        Lead::class => LeadPolicy::class,
        Note::class => NotePolicy::class,
        Challenge::class => ChallengePolicy::class,
        Claim::class => ClaimPolicy::class,
        Stage::class => StagePolicy::class,
        Feedback::class => FeedbackPolicy::class,

        // Выполненные работы
        OutcomesCategory::class => OutcomesCategoryPolicy::class,
        Outcome::class => OutcomePolicy::class,

        // Портфолио
        Portfolio::class => PortfolioPolicy::class,
        PortfoliosItem::class => PortfoliosItemPolicy::class,
        BusinessCase::class => BusinessCasePolicy::class,

        // Email рассылки
        Subscriber::class => SubscriberPolicy::class,
        TemplatesCategory::class => TemplatesCategoryPolicy::class,
        Template::class => TemplatePolicy::class,
        Mailing::class => MailingPolicy::class,
        MailingList::class => MailingListPolicy::class,
    ];

    public function boot()
    {
      $this->registerPolicies();
    }

}
