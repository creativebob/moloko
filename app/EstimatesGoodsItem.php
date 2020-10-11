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

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'estimate_id',
        'price_id',

        'goods_id',
        'currency_id',
        'sale_mode',

        'stock_id',

        'comment',

        'cost_unit',
        'price',
        'points',
        'count',

        'cost',
        'amount',

        'price_discount_id',
        'price_discount_unit',
        'price_discount',
        'total_price_discount',

        'catalogs_item_discount_id',
        'catalogs_item_discount_unit',
        'catalogs_item_discount',
        'total_catalogs_item_discount',

        'estimate_discount_id',
        'estimate_discount_unit',
        'estimate_discount',
        'total_estimate_discount',

        'client_discount_percent',
        'client_discount_unit_currency',
        'client_discount_currency',
        'total_client_discount',

        'total',
        'total_points',
        'total_bonuses',

        'computed_discount_percent',
        'computed_discount_currency',
        'total_computed_discount',

        'is_manual',
        'manual_discount_percent',
        'manual_discount_currency',
        'total_manual_discount',

        'discount_currency',
        'discount_percent',

        'margin_currency_unit',
        'margin_percent_unit',
        'margin_currency',
        'margin_percent',

        'is_reserved',

        'sort',

        'display',
        'system',
        'moderation',
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

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
