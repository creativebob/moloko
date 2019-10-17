<?php

namespace App\Models\Project;

use App\Models\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class EstimatesGoodsItem extends Model
{
    // Включаем кеш
    use Cachable;

    // use SoftDeletes;

    use Commonable;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'estimate_id',
        'price_id',
        'goods_id',

        'company_id',
        'author_id',

        'count',
        'price',

        'amount',

    ];

    // Смета
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function document()
    {
        return $this->belongsTo(Estimate::class, 'estimate_id');
    }

    // Прайс
    public function price()
    {
        return $this->belongsTo('App\PricesGoods');
    }

    // Товар
    public function goods()
    {
        return $this->belongsTo('App\Goods');
    }

    public function product()
    {
        return $this->belongsTo('App\Goods', 'goods_id');
    }

    public function cmv()
    {
        return $this->belongsTo('App\Goods', 'goods_id');
    }
}
