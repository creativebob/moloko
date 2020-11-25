<?php

namespace App\Http\Controllers\Project\Api\v1;

use App\Models\Project\CatalogsGoodsItem;
use App\PricesGoods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CatalogsGoodsItemController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Получаем полный раздел со всеми прайсами
        // TODO - 09.06.20 - Нужно какое то условие или настройка какие прайсы грузить (самого раздела, или вложенных в него)
        $catalogs_goods_item = CatalogsGoodsItem::with([

            // TODO - 02.07.20 - Используется на РХ
            'prices' => function ($q) use ($catalog_slug) {
                $q->with([
                    'goods' => function ($q) use ($catalog_slug) {
                        $q->with([
                            'related' => function ($q) use ($catalog_slug) {
                                $q->with([
                                    'prices' => function ($q) use ($catalog_slug) {
                                        $q->with([
                                            'catalogs_item.parent'
                                        ])
                                            ->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
                                                $q->where('slug', $catalog_slug);
                                            });
                                    }
                                ])
                                    ->whereHas('prices', function ($q) use ($catalog_slug) {
                                        $q->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
                                                $q->where('slug', $catalog_slug);
                                            });
                                    });
                            },
                        ]);
                    },
                    'currency',
                    'catalog',
                    'catalogs_item.directive_category'
                ]);
            },

            'childs_prices'  => function ($q) use ($catalog_slug) {
                $q->with([
                    'goods' => function ($q) use ($catalog_slug) {
                        $q->with([
                            'related' => function ($q) use ($catalog_slug) {
                                $q->with([
                                    'prices' => function ($q) use ($catalog_slug) {
                                        $q->with([
                                            'catalogs_item.parent'
                                        ])
                                            ->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
                                                $q->where('slug', $catalog_slug);
                                            });
                                    }
                                ])
                                    ->whereHas('prices', function ($q) use ($catalog_slug) {
                                        $q->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalog_slug) {
                                                $q->where('slug', $catalog_slug);
                                            });
                                    });
                            },
                        ]);
                    },
                    'currency',
                    'catalog',
                    'catalogs_item.directive_category'
                ]);
            },

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
    }
}
