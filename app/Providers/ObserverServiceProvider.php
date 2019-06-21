<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Категории артикулов
use App\RawsCategory;
use App\Observers\RawsCategoryObserver;
use App\EquipmentsCategory;
use App\Observers\EquipmentsCategoryObserver;

// Артикулы
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

// Процессы
use App\Process;
use App\Observers\ProcessObserver;

use App\Service;
use App\Observers\ServiceObserver;
use App\Workflow;
use App\Observers\WorkflowObserver;



// Новости
use App\Rubricator;
use App\Observers\RubricatorObserver;
use App\RubricatorsItem;
use App\Observers\RubricatorsItemObserver;
use App\News;
use App\Observers\NewsObserver;

use App\Stock;
use App\Observers\StockObserver;


// Сайт
use App\Site;
use App\Observers\SiteObserver;

// Каталоги
use App\CatalogsServicesItem;
use App\Observers\CatalogsServicesItemObserver;
use App\PricesService;
use App\Observers\PricesServiceObserver;

// Альбомы
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

        // Каталоги
        CatalogsServicesItem::observe(CatalogsServicesItemObserver::class);
        PricesService::observe(PricesServiceObserver::class);

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
