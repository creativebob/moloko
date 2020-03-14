<?php

namespace App;

use App\Models\System\Traits\Articlable;
use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Goods extends Model
{

	// Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    use Commonable;
    use Articlable;

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

    // Категория
    public function category()
    {
        return $this->belongsTo(GoodsCategory::class);
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(GoodsStock::class, 'cmv_id');
    }

    public function getRestAttribute()
    {
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
