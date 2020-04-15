<?php

namespace App\Http\View\Composers\Project;

use App\Article;
use App\PricesGoods;
use Illuminate\View\View;

class PricesGoodsFilterComposer
{
    public function compose(View $view)
    {

        $catalog_goods = $view->catalog_goods->load([
            'items_public'
        ]);

        // Разделы каталога
        $catalogs_goods_items = $catalog_goods->items_public->pluck('name', 'id');

        $catalog_goods_items_ids = $view->catalog_goods->items_public->pluck('id');

        $prices_goods = PricesGoods::with('goods_public')
            ->whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
            ->has('goods_public')
            ->public()
            ->get([
                'price',
                'catalogs_goods_item_id'
            ]);

        // Фильтр цены
        $price['step'] = 100;
        $price['min'] = $prices_goods->min('price');
        $price['max'] = $prices_goods->max('price');

        // Фильтр веса
        $articles_ids = [];
        foreach ($prices_goods as $price_goods) {
            $articles_ids[] = $price_goods->goods_public->article_id;
        }

        $articles = Article::find($articles_ids);

        $weight['step'] = '100';
        $weight['min'] = intval(($articles->min('weight_gram')*100)/100);
        $weight['max'] = intval(($articles->max('weight_gram')*100)/100);

        // Группы вложений
        $attachments_groups = [];
        foreach ($prices_goods as $price_goods) {
            $article = $price_goods->goods_public->article;

            if ($article->attachments->isNotEmpty()) {
                foreach ($article->attachments as $attachment) {
                    $attachments_groups[] = $attachment->article->group;
                }
            }
        }

        $attachments_groups = collect($attachments_groups)->unique();
        $attachments_groups = $attachments_groups->sortBy('name');

        return $view->with(compact('price', 'weight', 'attachments_groups', 'catalogs_goods_items'));
    }
}
