<?php

namespace App\Providers\System;

use App\AgencyScheme;
use App\ArticleCode;
use App\ArticlesGroup;
use App\Attachment;
use App\AttachmentsCategory;
use App\Competitor;
use App\EstimatesCancelGround;
use App\Event;
use App\EventsCategory;
use App\File;
use App\Impact;
use App\ImpactsCategory;
use App\Label;
use App\Mailing;
use App\MailingList;
use App\Models\System\Documents\Consignment;
use App\Models\System\Documents\ConsignmentsItem;
use App\Models\System\Documents\Production;
use App\Models\System\Documents\ProductionsItem;
use App\Models\System\Flows\EventsFlow;
use App\Models\System\Flows\ServicesFlow;
use App\Models\System\Stocks\AttachmentsStock;
use App\BusinessCase;
use App\CatalogsGoods;
use App\CatalogsGoodsItem;
use App\CatalogsService;
use App\Client;
use App\ClientsBlacklist;
use App\ClientsLoyaltiesScore;
use App\Company;
use App\Container;
use App\ContainersCategory;
use App\Models\System\Stocks\ContainersStock;
use App\ContractsClient;
use App\Cost;
use App\CostsHistory;
use App\Department;
use App\Direction;
use App\Discount;
use App\Domain;
use App\Employee;
use App\Models\System\Documents\Estimate;
use App\Models\System\Documents\EstimatesGoodsItem;
use App\Models\System\Documents\EstimatesServicesItem;
use App\ExpendablesCategory;
use App\Favourite;
use App\GoodsCategory;
use App\Models\System\Stocks\GoodsStock;
use App\Lead;
use App\Manufacturer;
use App\Agent;
use App\Menu;
use App\Metric;
use App\Models\System\Stocks\ImpactsStock;
use App\Models\System\Stocks\ToolsStock;
use App\Observers\System\AgencySchemeObserver;
use App\Observers\System\ArticleCodeObserver;
use App\Observers\System\ArticlesGroupObserver;
use App\Observers\System\AttachmentObserver;
use App\Observers\System\AttachmentsCategoryObserver;
use App\Observers\System\CompetitorObserver;
use App\Observers\System\Documents\ConsignmentObserver;
use App\Observers\System\Documents\ConsignmentsItemObserver;
use App\Observers\System\Documents\ProductionObserver;
use App\Observers\System\Documents\ProductionsItemObserver;
use App\Observers\System\EstimatesCancelGroundObserver;
use App\Observers\System\EventObserver;
use App\Observers\System\EventsCategoryObserver;
use App\Observers\System\FileObserver;
use App\Observers\System\Flows\EventsFlowObserver;
use App\Observers\System\Flows\ServicesFlowObserver;
use App\Observers\System\ImpactObserver;
use App\Observers\System\ImpactsCategoryObserver;
use App\Observers\System\LabelObserver;
use App\Observers\System\MailingListObserver;
use App\Observers\System\MailingObserver;
use App\Observers\System\OutletObserver;
use App\Observers\System\PhotoSettingObserver;
use App\Observers\System\ShiftObserver;
use App\Observers\System\Stocks\AttachmentsStockObserver;
use App\Observers\System\BusinessCaseObserver;
use App\Observers\System\CatalogsGoodsItemObserver;
use App\Observers\System\CatalogsGoodsObserver;
use App\Observers\System\CatalogsServiceObserver;
use App\Observers\System\ClientObserver;
use App\Observers\System\ClientsBlacklistObserver;
use App\Observers\System\ClientsLoyaltiesScoreObserver;
use App\Observers\System\CompanyObserver;
use App\Observers\System\ContainerObserver;
use App\Observers\System\ContainersCategoryObserver;
use App\Observers\System\Stocks\ContainersStockObserver;
use App\Observers\System\ContractsClientObserver;
use App\Observers\System\CostObserver;
use App\Observers\System\CostsHistoryObserver;
use App\Observers\System\DepartmentObserver;
use App\Observers\System\DirectionObserver;
use App\Observers\System\DiscountObserver;
use App\Observers\System\DomainObserver;
use App\Observers\System\EmployeeObserver;
use App\Observers\System\Documents\EstimateObserver;
use App\Observers\System\Documents\EstimatesGoodsItemObserver;
use App\Observers\System\Documents\EstimatesServicesItemObserver;
use App\Observers\System\ExpendablesCategoryObserver;
use App\Observers\System\FavouriteObserver;
use App\Observers\System\GoodsCategoryObserver;
use App\Observers\System\Stocks\GoodsStockObserver;
use App\Observers\System\LeadObserver;
use App\Observers\System\ManufacturerObserver;
use App\Observers\System\AgentObserver;
use App\Observers\System\MenuObserver;
use App\Observers\System\MetricObserver;
use App\Observers\System\OffObserver;
use App\Observers\System\OutcomeObserver;
use App\Observers\System\OutcomesCategoryObserver;
use App\Observers\System\PageObserver;
use App\Observers\System\PaymentObserver;
use App\Observers\System\PhotoObserver;
use App\Observers\System\PluginObserver;
use App\Observers\System\PortfolioObserver;
use App\Observers\System\PortfoliosItemObserver;
use App\Observers\System\PositionObserver;
use App\Observers\System\PricesGoodsHistoryObserver;
use App\Observers\System\PricesGoodsObserver;
use App\Observers\System\PricesServicesHistoryObserver;
use App\Observers\System\ProcessesGroupObserver;
use App\Observers\System\PromotionObserver;
use App\Observers\System\Stocks\ImpactsStockObserver;
use App\Observers\System\Stocks\RawsStockObserver;
use App\Observers\System\ReceiptObserver;
use App\Observers\System\RepresentativeObserver;
use App\Observers\System\ReserveObserver;
use App\Observers\System\ReservesHistoryObserver;
use App\Observers\System\RoomsCategoryObserver;
use App\Observers\System\SectorObserver;
use App\Observers\System\ServicesCategoryObserver;
use App\Observers\System\StafferObserver;
use App\Observers\System\Stocks\ToolsStockObserver;
use App\Observers\System\SubscriberObserver;
use App\Observers\System\SupplierObserver;
use App\Observers\System\TemplateObserver;
use App\Observers\System\TemplatesCategoryObserver;
use App\Observers\System\VectorObserver;
use App\Observers\System\VendorObserver;
use App\Observers\System\WorkflowsCategoryObserver;
use App\Observers\System\WorkplaceObserver;
use App\Off;
use App\Outcome;
use App\OutcomesCategory;
use App\Outlet;
use App\Page;
use App\Payment;
use App\Photo;
use App\PhotoSetting;
use App\Plugin;
use App\Portfolio;
use App\PortfoliosItem;
use App\Position;
use App\PricesGoods;
use App\PricesGoodsHistory;
use App\PricesServicesHistory;
use App\ProcessesGroup;
use App\Promotion;
use App\Models\System\Stocks\RawsStock;
use App\Receipt;
use App\Representative;
use App\Reserve;
use App\ReservesHistory;
use App\RoomsCategory;
use App\Sector;
use App\ServicesCategory;
use App\Shift;
use App\Staffer;
use App\Subscriber;
use App\Supplier;
use App\Template;
use App\TemplatesCategory;
use App\Vector;
use App\Vendor;
use App\WorkflowsCategory;
use App\Workplace;
use Illuminate\Support\ServiceProvider;
use App\RawsCategory;
use App\Observers\System\RawsCategoryObserver;
use App\ToolsCategory;
use App\Observers\System\ToolsCategoryObserver;
use App\Article;
use App\Observers\System\ArticleObserver;
use App\Goods;
use App\Observers\System\GoodsObserver;
use App\Raw;
use App\Observers\System\RawObserver;
use App\Tool;
use App\Observers\System\ToolObserver;
use App\Room;
use App\Observers\System\RoomObserver;
use App\Process;
use App\Observers\System\ProcessObserver;
use App\Service;
use App\Observers\System\ServiceObserver;
use App\Workflow;
use App\Observers\System\WorkflowObserver;
use App\Rubricator;
use App\Observers\System\RubricatorObserver;
use App\RubricatorsItem;
use App\Observers\System\RubricatorsItemObserver;
use App\News;
use App\Observers\System\NewsObserver;
use App\Stock;
use App\Observers\System\StockObserver;
use App\Site;
use App\Observers\System\SiteObserver;
use App\CatalogsServicesItem;
use App\Observers\System\CatalogsServicesItemObserver;
use App\PricesService;
use App\Observers\System\PricesServiceObserver;
use App\AlbumsCategory;
use App\Observers\System\AlbumsCategoryObserver;
use App\Album;
use App\Observers\System\AlbumObserver;
use App\User;
use App\Observers\System\UserObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        Company::observe(CompanyObserver::class);

        // ????????????????????????
        User::observe(UserObserver::class);

        // ??????????????????????????
        Representative::observe(RepresentativeObserver::class);

        Outlet::observe(OutletObserver::class);
        Workplace::observe(WorkplaceObserver::class);
        Lead::observe(LeadObserver::class);
        Label::observe(LabelObserver::class);
        Shift::observe(ShiftObserver::class);

        // HR
        Department::observe(DepartmentObserver::class);
        Position::observe(PositionObserver::class);
        Staffer::observe(StafferObserver::class);
        Employee::observe(EmployeeObserver::class);

        // ??????????????
        Metric::observe(MetricObserver::class);

        // ?????????????????? ??????????????????
        GoodsCategory::observe(GoodsCategoryObserver::class);
        RawsCategory::observe(RawsCategoryObserver::class);
        ContainersCategory::observe(ContainersCategoryObserver::class);
        AttachmentsCategory::observe(AttachmentsCategoryObserver::class);
        ToolsCategory::observe(ToolsCategoryObserver::class);
        RoomsCategory::observe(RoomsCategoryObserver::class);
        ExpendablesCategory::observe(ExpendablesCategoryObserver::class);
        ImpactsCategory::observe(ImpactsCategoryObserver::class);

        // ????????????????
        Article::observe(ArticleObserver::class);
        ArticleCode::observe(ArticleCodeObserver::class);
        ArticlesGroup::observe(ArticlesGroupObserver::class);
        Goods::observe(GoodsObserver::class);
        Raw::observe(RawObserver::class);
        Container::observe(ContainerObserver::class);
        Attachment::observe(AttachmentObserver::class);
        Tool::observe(ToolObserver::class);
        Room::observe(RoomObserver::class);
        Impact::observe(ImpactObserver::class);


        // ?????????????????? ??????????????????
        ServicesCategory::observe(ServicesCategoryObserver::class);
        EventsCategory::observe(EventsCategoryObserver::class);
        WorkflowsCategory::observe(WorkflowsCategoryObserver::class);

        // ????????????????
        Process::observe(ProcessObserver::class);
        ProcessesGroup::observe(ProcessesGroupObserver::class);
        Service::observe(ServiceObserver::class);
        Event::observe(EventObserver::class);
        Workflow::observe(WorkflowObserver::class);

        // ????????????
        Discount::observe(DiscountObserver::class);

        // ??????????????????????
        Direction::observe(DirectionObserver::class);

        // ??????????????????
        Consignment::observe(ConsignmentObserver::class);
        ConsignmentsItem::observe(ConsignmentsItemObserver::class);

        // ????????????
        Production::observe(ProductionObserver::class);
        ProductionsItem::observe(ProductionsItemObserver::class);

        // ??????????
        Estimate::observe(EstimateObserver::class);
        EstimatesCancelGround::observe(EstimatesCancelGroundObserver::class);
        EstimatesGoodsItem::observe(EstimatesGoodsItemObserver::class);
        EstimatesServicesItem::observe(EstimatesServicesItemObserver::class);

        // ??????????????
        Reserve::observe(ReserveObserver::class);
        ReservesHistory::observe(ReservesHistoryObserver::class);

        // ?????????????????????? ???? ??????????
        Receipt::observe(ReceiptObserver::class);

        // ???????????????? ???? ????????????
        Off::observe(OffObserver::class);

        // ????????????
        Stock::observe(StockObserver::class);
        RawsStock::observe(RawsStockObserver::class);
        ContainersStock::observe(ContainersStockObserver::class);
        AttachmentsStock::observe(AttachmentsStockObserver::class);
        GoodsStock::observe(GoodsStockObserver::class);
        ToolsStock::observe(ToolsStockObserver::class);
        ImpactsStock::observe(ImpactsStockObserver::class);

        Cost::observe(CostObserver::class);
        CostsHistory::observe(CostsHistoryObserver::class);

        // ????????????
        EventsFlow::observe(EventsFlowObserver::class);
        ServicesFlow::observe(ServicesFlowObserver::class);

        // ????????????????
        ContractsClient::observe(ContractsClientObserver::class);

        Payment::observe(PaymentObserver::class);

        // ??????????????
        Rubricator::observe(RubricatorObserver::class);
        RubricatorsItem::observe(RubricatorsItemObserver::class);
        News::observe(NewsObserver::class);


        // ??????????
        Domain::observe(DomainObserver::class);
        Site::observe(SiteObserver::class);
        Page::observe(PageObserver::class);
        Menu::observe(MenuObserver::class);
        Promotion::observe(PromotionObserver::class);

        // ??????????????
        Plugin::observe(PluginObserver::class);

        // ???????????????? ??????????
        CatalogsGoods::observe(CatalogsGoodsObserver::class);
        CatalogsServicesItem::observe(CatalogsServicesItemObserver::class);
        PricesService::observe(PricesServiceObserver::class);
        PricesServicesHistory::observe(PricesServicesHistoryObserver::class);

        // ???????????????? ??????????????
        CatalogsService::observe(CatalogsServiceObserver::class);
        CatalogsGoodsItem::observe(CatalogsGoodsItemObserver::class);
        PricesGoods::observe(PricesGoodsObserver::class);
        PricesGoodsHistory::observe(PricesGoodsHistoryObserver::class);

        // ??????????????
        AlbumsCategory::observe(AlbumsCategoryObserver::class);
        Album::observe(AlbumObserver::class);
        Photo::observe(PhotoObserver::class);
        Vector::observe(VectorObserver::class);

        PhotoSetting::observe(PhotoSettingObserver::class);

        // ??????????????
        Sector::observe(SectorObserver::class);

        Favourite::observe(FavouriteObserver::class);

        // ??????????????
        Supplier::observe(SupplierObserver::class);
        Vendor::observe(VendorObserver::class);
        Manufacturer::observe(ManufacturerObserver::class);
        Agent::observe(AgentObserver::class);
        AgencyScheme::observe(AgencySchemeObserver::class);
        Competitor::observe(CompetitorObserver::class);

        // ????????????????????
        Client::observe(ClientObserver::class);
        ClientsLoyaltiesScore::observe(ClientsLoyaltiesScoreObserver::class);
        ClientsBlacklist::observe(ClientsBlacklistObserver::class);

        // ?????????????????????? ????????????
        OutcomesCategory::observe(OutcomesCategoryObserver::class);
        Outcome::observe(OutcomeObserver::class);

        // ??????????????????
        Portfolio::observe(PortfolioObserver::class);
        PortfoliosItem::observe(PortfoliosItemObserver::class);
        BusinessCase::observe(BusinessCaseObserver::class);

        // Email ????????????????
        Subscriber::observe(SubscriberObserver::class);
        TemplatesCategory::observe(TemplatesCategoryObserver::class);
        Template::observe(TemplateObserver::class);
        Mailing::observe(MailingObserver::class);
        MailingList::observe(MailingListObserver::class);
//        Dispatch::observe(DispatchObserver::class);

        File::observe(FileObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
