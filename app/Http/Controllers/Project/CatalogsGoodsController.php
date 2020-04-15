<?php

namespace App\Http\Controllers\Project;

use App\CatalogsGoods;
use App\Http\Controllers\Project\Traits\Commonable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CatalogsGoodsController extends Controller
{

    use Commonable;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-goods')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalog_goods = CatalogsGoods::with([
//            'items_public' => function ($q) use ($site) {
//                $q->with([
//                    'display_mode',
//                    'filters.values',
//                    'directive_category:id,alias',
//                    'childs',
//                ]);
//            },
            'prices' => function ($q) use ($site) {
                $q->with([
                    'goods_public' => function ($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'photo',
                                    'unit',
                                    'unit_weight',
                                    'manufacturer.company',
                                    'raws' => function ($q) {
                                        $q->with([
                                            'article' => function ($q) {
                                                $q->with([
                                                    'unit',
                                                    'photo',
                                                    'manufacturer.company'
                                                ]);
                                            },
                                            'metrics'
                                        ]);
                                    },
                                    'attachments' => function ($q) {
                                        $q->with([
                                            'article' => function ($q) {
                                                $q->with([
//                                                    'unit',
                                                    'photo',
                                                    'manufacturer.company'
                                                ]);
                                            },
                                        ]);
                                    },
                                    'containers' => function ($q) {
                                        $q->with([
                                            'article' => function ($q) {
                                                $q->with([
//                                                    'unit',
                                                    'photo',
                                                    'manufacturer.company'
                                                ]);
                                            },
                                        ]);
                                    },
                                ]);
                            },
                            'metrics',
                        ]);
                    },
                    'currency',
                ])
                    ->has('goods_public')
                    ->public()
                    ->filter()
                    ->where([
                        'filial_id' => $site->filial->id
                    ])
                    ->orderBy('sort', 'asc');
            },
        ])
            ->whereHas('filials', function ($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->where('slug', $slug)
            ->where([
                'display' => true
            ])
            ->first();
//        dd($catalog_goods);


//        dd($catalog_goods->prices());

        // Проверим, а доступен ли каталог товаров. Если нет, то кидаем ошибку
        if ($catalog_goods) {
            return view($site->alias.'.pages.catalogs_goods.index', compact('site',  'page', 'catalog_goods'));
        } else {
            abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...');
        }
//            if($catalog_item_slug){
//
//            // Получаем разделы прайса ограниченный slug'ом
//            $catalog_goods_items = $catalog_goods->items_public->where('slug', $catalog_item_slug)->first();
//            // dd($catalog_goods_items);
//
//            if($catalog_goods_items){
//
//                $page->title = $catalog_goods_items->title;
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
//            ->paginate(16);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
