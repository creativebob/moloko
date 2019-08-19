<?php

namespace App\Http\Controllers\Project;

use App\CatalogsGoodsItem;
use App\PricesGoods;
use App\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{

    // Настройки контроллера
    public function __construct(Request $request)
    {
//        $domain = $request->getHttpHost();
        $domain = $request->getHost();
//        dd($domain);

        $site = Site::where('domain', $domain)
        ->with([
            'pages_public',
            'filials'
            ])
            ->first();
//        dd($site);

        $this->site = $site;
    }

    public function start(Request $request)
    {
        if (is_null($this->site)) {
            return view('project.pages.mains.main');
        } else {
            $site = $this->site;
            $page = $site->pages_public
                ->where('alias'. 'main')
                ->first();

            return view($site->alias.'.pages.mains.index', compact('site','page'));
        }
    }

    public function catalogs_goods(Request $request, $catalog_slug, $catalog_item_slug)
    {
        $site = $this->site;

        $page = $site->pages_public->where('alias', 'catalogs_goods')->first();
        $page->title = "Подарки в текстильной упаковки";

        $catalog_goods_item = CatalogsGoodsItem::whereHas('catalog_public', function ($q) use ($site, $catalog_slug) {
            $q->whereHas('sites', function ($q) use ($site) {
                $q->where('id', $site->id);
            })
                ->where('slug', $catalog_slug);
        })
        ->where([
            'slug' => $catalog_item_slug,
            'display' => true

        ])
            ->first();
//        dd($catalog_goods_item);

        $prices_goods = PricesGoods::with([
            'goods_public'
        ])
            ->has('goods_public')
            ->where([
                'display' => true,
                'archive' => false
            ])
            ->get();
//        dd($prices_goods);


        return view($site->alias.'.pages.catalogs_goods.index', compact('site','page', 'catalog_goods_item', 'prices_goods'));


    }

    public function catalogs_services(Request $request, $catalog_slug, $catalog_item_slug)
    {
        $site = $this->site;

        // Вытаскивает через сайт каталог и его пункт с прайсами (не архивными), товаром и артикулом
        $site->load(['catalogs_services' => function ($q) use ($catalog_slug, $catalog_item_slug) {
            $q->with([
                'items' => function($q) use ($catalog_item_slug) {
                    $q->with([
                        'prices_services' => function ($q) {
                            $q->with([
                                'service' => function ($q) {
                                    $q->with(['process' => function ($q) {
                                        $q->where([
                                            'draft' => false
                                        ]);
                                    }])
                                        ->where([
                                            'display' => true,
                                            'archive' => false
                                        ]);
                                }
                            ])
                                ->where([
                                    'display' => true,
                                    'archive' => false
                                ]);
                        }
                    ])
                        ->where([
                            'slug' => $catalog_item_slug,
                            'display' => true,
                        ]);
                }
            ])
                ->where([
                    'slug' => $catalog_slug,
                    'display' => true,
                ]);
        }]);
        dd($site->catalogs_services->first()->items->first());
    }

    public function price_goods(Request $request, $id)
    {
        $site = $this->site;

        // Вытаскивает через сайт каталог и его пункт с прайсами (не архивными), товаром и артикулом
        $site->load(['catalogs_services' => function ($q) use ($catalog_slug, $catalog_item_slug) {
            $q->with([
                'items' => function($q) use ($catalog_item_slug) {
                    $q->with([
                        'prices_services' => function ($q) {
                            $q->with([
                                'service' => function ($q) {
                                    $q->with(['process' => function ($q) {
                                        $q->where([
                                            'draft' => false
                                        ]);
                                    }])
                                        ->where([
                                            'display' => true,
                                            'archive' => false
                                        ]);
                                }
                            ])
                                ->where([
                                    'display' => true,
                                    'archive' => false
                                ]);
                        }
                    ])
                        ->where([
                            'slug' => $catalog_item_slug,
                            'display' => true,
                        ]);
                }
            ])
                ->where([
                    'slug' => $catalog_slug,
                    'display' => true,
                ]);
        }]);
        dd($site->catalogs_services->first()->items->first());
    }
}
