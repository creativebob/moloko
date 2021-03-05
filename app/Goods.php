<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Stocks\GoodsStock;
use App\Models\System\Traits\Cmvable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Goods extends BaseModel
{
    use SoftDeletes,
        Cmvable;
    //    use Cachable;

    const ALIAS = 'goods';
    const DEPENDENCE = false;

    protected $fillable = [
        'article_id',
        'category_id',
        'price_unit_category_id',
        'price_unit_id',

        'is_produced',
        'is_ordered',

        'archive',
        'serial',

        'display',
        'system',
        'moderation'
    ];

    public function category()
    {
        return $this->belongsTo(GoodsCategory::class);
    }

    public function stocks()
    {
        return $this->hasMany(GoodsStock::class, 'cmv_id');
    }

    public function getRestAttribute()
    {
        // TODO - 17.10.20 - Выбирать склад по правильному филиалу
        if(!empty($this->hasMany(GoodsStock::class, 'cmv_id')->where('filial_id', 1)->first())){
            return $this->hasMany(GoodsStock::class, 'cmv_id')->where('filial_id', 1)->first()->free;
        } else {
            return null;
        }
    }

    // Пункты каталога
    public function catalogs_items()
    {
        return $this->belongsToMany(CatalogsGoodsItem::class, 'prices_goods', 'goods_id', 'catalogs_goods_item_id');
    }

    // Пункты каталога
    public function prices()
    {
        return $this->hasMany(PricesGoods::class)
            ->where('archive', false);
    }

    //
    public function price_unit()
    {
        return $this->belongsTo(Unit::class, 'price_unit_id');
    }

    public function related()
    {
        return $this->belongsToMany(Goods::class, 'goods_related', 'goods_id', 'related_id');
    }

    public function relating()
    {
        return $this->belongsToMany(Goods::class, 'goods_related', 'related_id','goods_id');
    }

    public function relatingCategory()
    {
        return $this->belongsToMany(GoodsCategory::class, 'preset_related', 'goods_id', 'goods_category_id');
    }

    // Рабочие процессы
    public function workflows()
    {
        return $this->morphMany(Workflow::class, 'workflows');
    }

    // Составы заказов
    public function order_compositions()
    {
        return $this->morphMany(OrderComposition::class, 'order_compositions');
    }

    public function in_kits()
    {
        return $this->belongsToMany(Article::class, 'article_goods', 'goods_id', 'article_id');
    }

    // Метрики
    public function metrics()
    {
        return $this->belongsToMany('App\Metric', 'goods_metric')
            ->withPivot('value');
    }

    public function getMetricByName($metric_name){

        $metric = $this->metrics->where('name', $metric_name)->first();

        if(isset($metric)){

            if($metric->property->type == 'list'){
                return $metric->values->where('id', $metric->pivot->value)->first()->value;
            }
            return $metric->pivot->value;

        } else {
            return null;
        }
    }

    public function getMetricById($metric_id){

        $metric = $this->metrics->where('id', $metric_id)->first();

        if(isset($metric)){

            if($metric->property->type == 'list'){
                return $metric->values->where('id', $metric->pivot->value)->first()->value;
            }
            return $metric->pivot->value;

        } else {
            return null;
        }
    }
}
