<?php

namespace App\Http\View\Composers\Project;

use App\CatalogsGoods;
use Illuminate\View\View;

class SubCatalogsGoodsItemsComposer
{
    public function compose(View $view)
    {
        $catalogs_goods_item = $view->catalogs_goods_item;
//        dd($catalogs_goods_item);

        // TODO - 14.04.20 - Уже ближе к универсальности, но все равно пока заточено под РХ
        $parent = null;
        if ($catalogs_goods_item->level == 1) {
            $site = $view->site;

            $catalogs_goods_item->load([
                'childs_prices',
                'catalog',
//                => function ($q) use ($site) {
//                    $q->with([
//                        'goods' => function ($q) {
//                            $q->with([
//                                'article' => function ($q) {
//                                    $q->with([
//                                        'photo',
//                                        'raws' => function ($q) {
//                                            $q->with([
//                                                'article.unit',
//                                                'metrics'
//                                            ]);
//                                        },
//                                        'attachments' => function ($q) {
//                                            $q->with([
//                                                'article.unit',
//                                            ]);
//                                        },
//                                        'containers' => function ($q) {
//                                            $q->with([
//                                                'article.unit',
//                                            ]);
//                                        },
//                                    ]);
//                                },
//                                'metrics',
//                            ]);
//                        },
//                        'currency',
//
//                    ])
//                        ->where([
//                            'prices_goods.filial_id' => $site->filial->id
//                        ])
//                        ->orderBy('sort', 'asc');
//                }

                'directive_category:id,alias',
            ]);

            $parent = $catalogs_goods_item;

        } else {
            $catalogs_goods_item->load([
                'parent' => function ($q) {
                    $q->with([
                        'childs',
                        'catalog'
                    ]);
                }
            ]);

            $parent = $catalogs_goods_item->parent;
        }
//        dd($parent);

        return $view->with(compact('parent'));
    }

}
