<?php

namespace App\Providers;

use App\ArticlesGroup;
use App\Attachment;
use App\AttachmentsCategory;
use App\AttachmentsStock;
use App\CatalogsGoods;
use App\CatalogsGoodsItem;
use App\CatalogsService;
use App\Consignment;
use App\ConsignmentsItem;
use App\Container;
use App\ContainersCategory;
use App\ContainersStock;
use App\Cost;
use App\CostsHistory;
use App\Direction;
use App\Estimate;
use App\EstimatesGoodsItem;
use App\EstimatesServicesItem;
use App\ExpendablesCategory;
use App\Favourite;
use App\GoodsCategory;
use App\GoodsStock;
use App\Menu;
use App\Metric;
use App\Observers\ArticlesGroupObserver;
use App\Observers\AttachmentObserver;
use App\Observers\AttachmentsCategoryObserver;
use App\Observers\AttachmentsStockObserver;
use App\Observers\CatalogsGoodsItemObserver;
use App\Observers\CatalogsGoodsObserver;
use App\Observers\CatalogsServiceObserver;
use App\Observers\ConsignmentObserver;
use App\Observers\ConsignmentsItemObserver;
use App\Observers\ContainerObserver;
use App\Observers\ContainersCategoryObserver;
use App\Observers\ContainersStockObserver;
use App\Observers\CostObserver;
use App\Observers\CostsHistoryObserver;
use App\Observers\DirectionObserver;
use App\Observers\EstimateObserver;
use App\Observers\EstimatesGoodsItemObserver;
use App\Observers\EstimatesServicesItemObserver;
use App\Observers\ExpendablesCategoryObserver;
use App\Observers\FavouriteObserver;
use App\Observers\GoodsCategoryObserver;
use App\Observers\GoodsStockObserver;
use App\Observers\MenuObserver;
use App\Observers\MetricObserver;
use App\Observers\OffObserver;
use App\Observers\PageObserver;
use App\Observers\PhotoObserver;
use App\Observers\PluginObserver;
use App\Observers\PricesGoodsHistoryObserver;
use App\Observers\PricesGoodsObserver;
use App\Observers\PricesServicesHistoryObserver;
use App\Observers\ProcessesGroupObserver;
use App\Observers\ProductionObserver;
use App\Observers\ProductionsItemObserver;
use App\Observers\RawsStockObserver;
use App\Observers\ReceiptObserver;
use App\Observers\ReserveObserver;
use App\Observers\ReservesHistoryObserver;
use App\Observers\RoomsCategoryObserver;
use App\Observers\SectorObserver;
use App\Observers\ServicesCategoryObserver;
use App\Observers\StafferObserver;
use App\Observers\WorkflowsCategoryObserver;
use App\Off;
use App\Page;
use App\Photo;
use App\Plugin;
use App\PricesGoods;
use App\PricesGoodsHistory;
use App\PricesServicesHistory;
use App\ProcessesGroup;
use App\Production;
use App\ProductionsItem;
use App\RawsStock;
use App\Receipt;
use App\Reserve;
use App\ReservesHistory;
use App\RoomsCategory;
use App\Sector;
use App\ServicesCategory;
use App\Staffer;
use App\WorkflowsCategory;
use Illuminate\Support\ServiceProvider;
use App\RawsCategory;
use App\Observers\RawsCategoryObserver;
use App\ToolsCategory;
use App\Observers\ToolsCategoryObserver;
use App\Article;
use App\Observers\ArticleObserver;
use App\Goods;
use App\Observers\GoodsObserver;
use App\Raw;
use App\Observers\RawObserver;
use App\Tool;
use App\Observers\ToolObserver;
use App\Room;
use App\Observers\RoomObserver;
use App\Process;
use App\Observers\ProcessObserver;
use App\Service;
use App\Observers\ServiceObserver;
use App\Workflow;
use App\Observers\WorkflowObserver;
use App\Rubricator;
use App\Observers\RubricatorObserver;
use App\RubricatorsItem;
use App\Observers\RubricatorsItemObserver;
use App\News;
use App\Observers\NewsObserver;
use App\Stock;
use App\Observers\StockObserver;
use App\Site;
use App\Observers\SiteObserver;
use App\CatalogsServicesItem;
use App\Observers\CatalogsServicesItemObserver;
use App\PricesService;
use App\Observers\PricesServiceObserver;
use App\AlbumsCategory;
use App\Observers\AlbumsCategoryObserver;
use App\Album;
use App\Observers\AlbumObserver;
use App\User;
use App\Observers\UserObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // Пользователь
        User::observe(UserObserver::class);
        Staffer::observe(StafferObserver::class);

        // Метрики
        Metric::observe(MetricObserver::class);

        // Категории артикулов
        GoodsCategory::observe(GoodsCategoryObserver::class);
        RawsCategory::observe(RawsCategoryObserver::class);
        ContainersCategory::observe(ContainersCategoryObserver::class);
        AttachmentsCategory::observe(AttachmentsCategoryObserver::class);
        ToolsCategory::observe(ToolsCategoryObserver::class);
        RoomsCategory::observe(RoomsCategoryObserver::class);
        ExpendablesCategory::observe(ExpendablesCategoryObserver::class);

        // Артикулы
        Article::observe(ArticleObserver::class);
        ArticlesGroup::observe(ArticlesGroupObserver::class);
        Goods::observe(GoodsObserver::class);
        Raw::observe(RawObserver::class);
        Container::observe(ContainerObserver::class);
        Attachment::observe(AttachmentObserver::class);
        Tool::observe(ToolObserver::class);
        Room::observe(RoomObserver::class);


        // Категории процессов
        ServicesCategory::observe(ServicesCategoryObserver::class);
        WorkflowsCategory::observe(WorkflowsCategoryObserver::class);

        // Процессы
        Process::observe(ProcessObserver::class);
        ProcessesGroup::observe(ProcessesGroupObserver::class);
        Service::observe(ServiceObserver::class);
        Workflow::observe(WorkflowObserver::class);

        // Направления
        Direction::observe(DirectionObserver::class);

        // Накладные
        Consignment::observe(ConsignmentObserver::class);
        ConsignmentsItem::observe(ConsignmentsItemObserver::class);

        // Наряды
        Production::observe(ProductionObserver::class);
        ProductionsItem::observe(ProductionsItemObserver::class);

        // Сметы
        Estimate::observe(EstimateObserver::class);
        EstimatesGoodsItem::observe(EstimatesGoodsItemObserver::class);
        EstimatesServicesItem::observe(EstimatesServicesItemObserver::class);

        // Резервы
        Reserve::observe(ReserveObserver::class);
        ReservesHistory::observe(ReservesHistoryObserver::class);

        // Поступления на склад
        Receipt::observe(ReceiptObserver::class);

        // Списания со склада
        Off::observe(OffObserver::class);

        // Склады
        Stock::observe(StockObserver::class);
        RawsStock::observe(RawsStockObserver::class);
        ContainersStock::observe(ContainersStockObserver::class);
        AttachmentsStock::observe(AttachmentsStockObserver::class);
        GoodsStock::observe(GoodsStockObserver::class);

        Cost::observe(CostObserver::class);
        CostsHistory::observe(CostsHistoryObserver::class);

        // Новости
        Rubricator::observe(RubricatorObserver::class);
        RubricatorsItem::observe(RubricatorsItemObserver::class);
        News::observe(NewsObserver::class);


        // Сайты
        Site::observe(SiteObserver::class);
        Page::observe(PageObserver::class);
        Menu::observe(MenuObserver::class);

        // Плагины
        Plugin::observe(PluginObserver::class);

        // Каталоги услуг
        CatalogsGoods::observe(CatalogsGoodsObserver::class);
        CatalogsServicesItem::observe(CatalogsServicesItemObserver::class);
        PricesService::observe(PricesServiceObserver::class);
        PricesServicesHistory::observe(PricesServicesHistoryObserver::class);

        // Каталоги товаров
        CatalogsService::observe(CatalogsServiceObserver::class);
        CatalogsGoodsItem::observe(CatalogsGoodsItemObserver::class);
        PricesGoods::observe(PricesGoodsObserver::class);
        PricesGoodsHistory::observe(PricesGoodsHistoryObserver::class);

        // Альбомы
        AlbumsCategory::observe(AlbumsCategoryObserver::class);
        Album::observe(AlbumObserver::class);
        Photo::observe(PhotoObserver::class);

        // Сектора
        Sector::observe(SectorObserver::class);

        Favourite::observe(FavouriteObserver::class);
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
