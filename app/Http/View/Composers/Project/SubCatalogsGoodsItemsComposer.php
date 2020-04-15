<?php

namespace App\Http\View\Composers\Project;

use App\CatalogsGoods;
use Illuminate\View\View;

class SubCatalogsGoodsItemsComposer
{
    public function compose(View $view)
    {
        $catalogs_goods_item = $view->catalogs_goods_item;

        // TODO - 14.04.20 - Уже ближе к универсальности, но все равно пока заточено под РХ
        if ($catalogs_goods_item->level == 1) {
            $site = $view->site;

            $catalogs_goods_item->load([
                'childs_prices' => function ($q) use ($site) {
                    $q->with([
                        'goods' => function ($q) {
                            $q->with([
                                'article' => function ($q) {
                                    $q->with([
                                        'photo',
                                        'raws' => function ($q) {
                                            $q->with([
                                                'article.unit',
                                                'metrics'
                                            ]);
                                        },
                                        'attachments' => function ($q) {
                                            $q->with([
                                                'article.unit',
                                            ]);
                                        },
                                        'containers' => function ($q) {
                                            $q->with([
                                                'article.unit',
                                            ]);
                                        },
                                    ]);
                                },
                                'metrics',
                            ]);
                        },
                        'currency',
                        'catalogs_item.directive_category:id,alias',
                    ])
                        ->where([
                            'prices_goods.filial_id' => $site->filial->id
                        ])
                        ->orderBy('sort', 'asc');
                }
            ]);

        } else {
            $catalogs_goods_item->load([
                'parent' => function ($q) {
                    $q->with([
                        'childs'
                    ]);
                }
            ]);
        }
//        dd($catalogs_goods_item);

        return $view->with(compact('catalogs_goods_item'));
    }

}
