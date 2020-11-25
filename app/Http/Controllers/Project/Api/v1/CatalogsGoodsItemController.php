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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {

        $catalogId = $request->catalog_id;
        // Получаем полный раздел со всеми прайсами
        // TODO - 09.06.20 - Нужно какое то условие или настройка какие прайсы грузить (самого раздела, или вложенных в него)
        $catalogsGoodsItem = CatalogsGoodsItem::with([
//
//            // TODO - 02.07.20 - Используется на РХ
            'prices' => function ($q) use ($catalogId) {
                $q->with([
                    'goods' => function ($q) use ($catalogId) {
                        $q->with([
                            'related' => function ($q) use ($catalogId) {
                                $q->with([
                                    'prices' => function ($q) use ($catalogId) {
                                        $q->with([
                                            'catalogs_item.parent'
                                        ])
                                            ->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalogId) {
                                                $q->where('id', $catalogId);
                                            });
                                    }
                                ])
                                    ->whereHas('prices', function ($q) use ($catalogId) {
                                        $q->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalogId) {
                                                $q->where('id', $catalogId);
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
//
            'childs_prices'  => function ($q) use ($catalogId) {
                $q->with([
                    'goods' => function ($q) use ($catalogId) {
                        $q->with([
                            'related' => function ($q) use ($catalogId) {
                                $q->with([
                                    'prices' => function ($q) use ($catalogId) {
                                        $q->with([
                                            'catalogs_item.parent'
                                        ])
                                            ->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalogId) {
                                                $q->where('id', $catalogId);
                                            });
                                    }
                                ])
                                    ->whereHas('prices', function ($q) use ($catalogId) {
                                        $q->where('display', true)
                                            ->where('archive', false)
                                            ->whereHas('catalog', function ($q) use ($catalogId) {
                                                $q->where('id', $catalogId);
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
//
//            'directive_category:id,alias',
//            'filters.values',
//            'catalog'
        ])
            ->display()
            ->find($request->catalogs_item_id);
//        dd($catalogsGoodsItem);

        $pricesGoods = [];

        if ($catalogsGoodsItem->prices->isNotEmpty()) {
            $pricesGoods = $catalogsGoodsItem->prices;
        }

        if ($catalogsGoodsItem->childs_prices->isNotEmpty()) {
            $pricesGoods = $catalogsGoodsItem->childs_prices;
        }

        return response()->json($pricesGoods);
    }
}
