<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogsGoodsItem extends Model
{
    use Publicable;
    use Cachable;
    use SoftDeletes;

    protected $with = [
        'directive_category'
    ];

    // Каталог
    public function catalog()
    {
        return $this->belongsTo(CatalogsGoods::class, 'catalogs_goods_id')
            ->display();
    }

    // Родитель
    public function parent()
    {
        return $this->belongsTo(CatalogsGoodsItem::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(CatalogsGoodsItem::class, 'parent_id')
            ->display()
            ->sort();
    }

    // Главный
    public function category()
    {
        return $this->belongsTo(CatalogsGoodsItem::class);
    }

    // SEO
    public function seo()
    {
        return $this->belongsTo('App\Seo');
    } 

    // Прайсы
    public function prices()
    {
        return $this->hasMany(PricesGoods::class)
            ->display()
            ->archive()
            ->has('goods')
            ->sort();
    }


    public function childs_prices()
    {
        return $this->hasManyThrough(PricesGoods::class, CatalogsGoodsItem::class, 'parent_id', 'catalogs_goods_item_id')
            ->display(true, 'prices_goods')
            ->archive(false, 'prices_goods')
            ->has('goods')
            ->sort();
    }

    // Фильтры
    public function filters()
    {
        return $this->belongsToMany('App\Metric', 'filters_goods', 'catalogs_goods_item_id', 'metric_id');
    }

    public function directive_category()
    {
        return $this->belongsTo('App\UnitsCategory');
    }

    public function getNameWithParentAttribute()
    {
        if($this->parent_id != null){
            return $this->parent->name . ' / ' . $this->name;
        } else {
            return $this->name;
        }
    }

}
