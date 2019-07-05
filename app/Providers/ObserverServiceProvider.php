<?php

namespace App\Providers;

use App\CatalogsGoodsItem;
use App\Observers\CatalogsGoodsItemObserver;
use App\Observers\PageObserver;
use App\Observers\PluginObserver;
use App\Observers\PricesGoodsObserver;
use App\Page;
use App\Plugin;
use App\PricesGoods;
use Illuminate\Support\ServiceProvider;

use App\RawsCategory;
use App\Observers\RawsCategoryObserver;
use App\EquipmentsCategory;
use App\Observers\EquipmentsCategoryObserver;
use App\Article;
use App\Observers\ArticleObserver;
use App\Goods;
use App\Observers\GoodsObserver;
use App\Raw;
use App\Observers\RawObserver;
use App\Equipment;
use App\Observers\EquipmentObserver;
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


class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // Категории артикулов
        RawsCategory::observe(RawsCategoryObserver::class);
        EquipmentsCategory::observe(EquipmentsCategoryObserver::class);

        // Артикулы
        Article::observe(ArticleObserver::class);
        Goods::observe(GoodsObserver::class);
        Raw::observe(RawObserver::class);
        Equipment::observe(EquipmentObserver::class);
        Room::observe(RoomObserver::class);

        // Процессы
        Process::observe(ProcessObserver::class);
        Service::observe(ServiceObserver::class);
        Workflow::observe(WorkflowObserver::class);



        // Склады
        Stock::observe(StockObserver::class);

        // Новости
        Rubricator::observe(RubricatorObserver::class);
        RubricatorsItem::observe(RubricatorsItemObserver::class);
        News::observe(NewsObserver::class);

        // Сайты
        Site::observe(SiteObserver::class);
        Page::observe(PageObserver::class);

        // Плагины
        Plugin::observe(PluginObserver::class);

        // Каталоги
        CatalogsServicesItem::observe(CatalogsServicesItemObserver::class);
        PricesService::observe(PricesServiceObserver::class);

        CatalogsGoodsItem::observe(CatalogsGoodsItemObserver::class);
        PricesGoods::observe(PricesGoodsObserver::class);

        // Альбомы
        AlbumsCategory::observe(AlbumsCategoryObserver::class);
        Album::observe(AlbumObserver::class);
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
