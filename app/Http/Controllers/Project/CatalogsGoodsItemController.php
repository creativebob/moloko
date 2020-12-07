<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Models\Project\CatalogsGoodsItem;
use Illuminate\Http\Request;

class CatalogsGoodsItemController extends BaseController
{
    /**
     * CatalogsGoodsItemController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function show($catalog_slug, $slug)
    {
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-goods-items')->first();

        // Получаем полный раздел со всеми прайсами
        // TODO - 09.06.20 - Нужно какое то условие или настройка какие прайсы грузить (самого раздела, или вложенных в него)
        $catalogs_goods_item = CatalogsGoodsItem::with([

            // TODO - 02.07.20 - Используется на РХ
//            'prices' => function ($q) use ($catalog_slug) {
//                $q->with([
//                    'goods' => function ($q) use ($catalog_slug) {
//                        $q->with([
//                            'related' => function ($q) use ($catalog_slug) {
//                                $q->with([
//                                    'prices' => function ($q) use ($catalog_slug) {
//                                        $q->with([
//                                            'catalogs_item.parent'
//                                        ])
//                                            ->where('display', true)
//                                            ->where('archive', false)
//                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
//                                                $q->where('slug', $catalog_slug);
//                                            });
//                                    }
//                                ])
//                                    ->whereHas('prices', function ($q) use ($catalog_slug) {
//                                        $q->where('display', true)
//                                            ->where('archive', false)
//                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
//                                                $q->where('slug', $catalog_slug);
//                                            });
//                                    });
//                            },
//                        ]);
//                    },
//                    'currency',
//                    'catalog',
//                    'catalogs_item.directive_category'
//                ]);
//            },
//
//            'childs_prices'  => function ($q) use ($catalog_slug) {
//                $q->with([
//                    'goods' => function ($q) use ($catalog_slug) {
//                        $q->with([
//                            'related' => function ($q) use ($catalog_slug) {
//                                $q->with([
//                                    'prices' => function ($q) use ($catalog_slug) {
//                                        $q->with([
//                                            'catalogs_item.parent'
//                                        ])
//                                            ->where('display', true)
//                                            ->where('archive', false)
//                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
//                                                $q->where('slug', $catalog_slug);
//                                            });
//                                    }
//                                ])
//                                    ->whereHas('prices', function ($q) use ($catalog_slug) {
//                                        $q->where('display', true)
//                                            ->where('archive', false)
//                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
//                                                $q->where('slug', $catalog_slug);
//                                            });
//                                    });
//                            },
//                        ]);
//                    },
//                    'currency',
//                    'catalog',
//                    'catalogs_item.directive_category'
//                ]);
//            },

            'directive_category:id,alias',
            'filters.values',
            'catalog'
        ])
            ->where('slug', $slug)
            ->whereHas('catalog', function ($q) use ($site, $catalog_slug) {
                $q->where('slug', $catalog_slug)
                    ->whereHas('filials', function ($q) use ($site) {
                        $q->where('id', $site->filial->id);
                    });
            })
            ->display()
            ->first();
//        dd($catalogs_goods_item);

        // Проверим, а доступен ли каталог товаров. Если нет, то кидаем ошибку
        if ($catalogs_goods_item) {
            return view("{$site->alias}.pages.catalogs_goods_items.index", compact('site',  'page', 'catalogs_goods_item'));
        } else {
            abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...');
        }

//        // Получаем полный прайс со всеми доступными разделами
//        $catalog_goods = CatalogsGoods::with([
//            'items_public' => function ($q) {
//                $q->with([
//                    'display_mode',
//                    'filters.values'
//                ]);
//            },
//        ])
//            ->whereHas('filials', function ($q) use ($site) {
//                $q->where('id', $site->filial->id);
//            })
//            ->where('slug', $catalog_slug)
//            ->where(['display' => true])
//            ->first();
//
//        // Проверим, а доступен ли каталог товаров. Если нет, то кидаем ошибку
//        if(!$catalog_goods){abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...'); }
//        if($catalog_item_slug){
//
//            // Получаем разделы прайса ограниченный slug'ом
//            $catalog_goods_items = $catalog_goods->items_public->where('slug', $catalog_item_slug)->first();
//            // dd($catalog_goods_items);
//
//            if($catalog_goods_items){
//
//                $page->title = $catalog_goods_items->title ?? $catalog_goods_items->name;
//                $page->description = $catalog_goods_items->seo_description;
//
//                $catalog_goods_items->load('childs');
//                //dd($catalog_goods_items);
//
//                $sub_menu = $catalog_goods->items_public->where('slug', getFirstSlug($catalog_item_slug))->first();
//                $sub_menu->load('childs');
//                $sub_menu_ids = $sub_menu->childs->pluck('id');
//
//                // Получаем id всех доступных на сайте разделов прайса,
//                // чтобы далее не заниматься повторным перебором при получении товаров
//                $catalog_goods_items_ids = $catalog_goods->items_public->where('slug', $catalog_item_slug)->pluck('id');
//
//
//            } else {
//
//                abort(404, 'Страница не найдена');
//            }
//
//
//        } else {
//
//            // Получаем все доступные разделы прайса
//            $catalog_goods_items = $catalog_goods->items_public;
//            $page->title = 'Все товары';
//            $catalog_goods_items_ids = $catalog_goods->items_public->pluck('id');
//        }
//
//
//        if($sub_menu_ids){
//            if(getFirstSlug($catalog_item_slug) == $catalog_item_slug){
//                $catalog_goods_items_ids = $catalog_goods_items_ids->merge($sub_menu_ids);
//            }
//        }
//
//        $prices_goods = PricesGoods::with([
//            'goods_public' => function ($q) {
//                $q->with([
//                    'article' => function ($q) {
//                        $q->with([
//                            'photo',
//                            'raws' => function ($q) {
//                                $q->with([
//                                    'article.unit',
//                                    'metrics'
//                                ]);
//                            },
//                            'attachments' => function ($q) {
//                                $q->with([
//                                    'article.unit',
//                                ]);
//                            },
//                            'containers' => function ($q) {
//                                $q->with([
//                                    'article.unit',
//                                ]);
//                            },
//                        ]);
//                    },
//                    'metrics',
//                ]);
//            },
//            'currency',
//            'catalogs_item.directive_category:id,alias'
//        ])
//            ->whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
//            ->has('goods_public')
//            ->where([
//                'display' => true,
//                'archive' => false,
//                'filial_id' => $this->site->filial->id
//            ])
//            ->filter(request())
//            ->orderBy('sort', 'asc')
//            ->paginate(50);


    }
}
