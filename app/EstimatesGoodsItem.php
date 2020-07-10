<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class EstimatesGoodsItem extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    use Commonable;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'estimate_id',

        'price_id',
        'currency_id',

        'goods_id',

        'stock_id',

        'company_id',
        'author_id',

        'count',
        'price',

        'cost',
        'points',

        'comment',

        'margin_percent',
        'margin_currency',

        'discount_percent',
        'discount_currency',


        'amount',
        'total',

        'is_reserved',

        'display',
        'system',
        'moderation'
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
        return $this->belongsTo(PricesGoods::class);
    }

    public function price_goods()
    {
        return $this->belongsTo(PricesGoods::class, 'price_id');
    }

    // Товар
    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function product()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function cmv()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    // Резерв
    public function reserve()
    {
        return $this->morphOne(Reserve::class, 'documents_item');
    }

    // Склад
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
