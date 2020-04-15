<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;


class PricesGoods extends Model
{
    use Publicable;
    use Cachable;

    protected $table = 'prices_goods';

    protected $with = ['goods'];

    // Каталог
    public function catalog()
    {
        return $this->belongsTo(CatalogsGoods::class, 'catalogs_goods_id')
            ->display();
    }

    // Раздел
    public function catalogs_item()
    {
        return $this->belongsTo(CatalogsGoodsItem::class, 'catalogs_goods_item_id')
            ->display();
    }

    // Филиал
    public function filial()
    {
        return $this->belongsTo('App\Department');
    }

    // Товары
    public function goods()
    {
        return $this->belongsTo(Goods::class)
            ->display()
            ->archive()
            ->has('article')
            ;
    }

    // История
    public function history()
    {
        return $this->hasMany('App\PricesGoodsHistory', 'prices_goods_id');
    }

    // Актуальная цена
    public function actual_price()
    {
        return $this->hasOne('App\PricesGoodsHistory', 'prices_goods_id')
            ->whereNull('end_date');
    }

    // Предок
    public function ancestor()
    {
        return $this->belongsTo('App\PricesGoods');
    }

    // Последователь
    public function follower()
    {
        return $this->hasOne('App\PricesGoods', 'ancestor_id')
            ->where('archive', false);
    }

    // Общее отношение для товаров и услуг
    public function product()
    {
        return $this->belongsTo('App\Goods', 'goods_id');
    }

    // Валюта
    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    // Фильтр
    public function scopeFilter($query)
    {
        if (request('price')) {
            $price = request('price');
            if (isset($price['min'])) {
                $query->where('price', '>=', $price['min']);
            }
            if (isset($price['max'])) {
                $query->where('price', '<=', $price['max']);
            }
            $query->orderBy('price');
        }

        if (request('weight')) {
            $weight = request('weight');
            $query->whereHas('goods', function($q) use ($weight) {
                $q->whereHas('article', function($q) use ($weight) {
                    $q->where('weight', '>=', $weight['min'] / 1000)
                        ->where('weight', '<=', $weight['max'] / 1000);
                });
            });
        }

        if (request('catalogs_goods_item')) {
            $catalogs_goods_item = request('catalogs_goods_item');
            $query->where('catalogs_goods_item_id', $catalogs_goods_item);
        }

        if (request('raws_articles_groups')) {
            $raws_articles_groups = request('raws_articles_groups');
//		    dd($raws_articles_groups);

            $query->whereHas('goods', function($q) use ($raws_articles_groups) {
                $q->whereHas('article', function($q) use ($raws_articles_groups) {
                    foreach($raws_articles_groups as $item){
                        $q->whereHas('attachments',function($q) use ($item) {
                            $q->whereHas('article', function ($q) use ($item) {
                                $q->where('articles_group_id', $item);
                            });
                        });
                    }
                });
            });
        }

        return $query;
    }

}
