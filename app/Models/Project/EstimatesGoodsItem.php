<?php

namespace App\Models\Project;

use App\Models\Project\Traits\Publicable;
use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstimatesGoodsItem extends Model
{

    use Publicable;
    use Commonable;
    use Cachable;
    use SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'estimate_id',
        'price_id',
        'goods_id',
        'currency_id',
        'stock_id',
        'sale_mode',

        'comment',

        'cost_unit',
        'price',
        'points',
        'count',

        'price_discount_id',
        'price_discount_unit',

        'catalogs_item_discount_id',
        'catalogs_item_discount_unit',

        'estimate_discount_id',
        'estimate_discount_unit',

        'client_discount_percent',

        'manual_discount_percent',
        'manual_discount_currency',

        'is_reserved',

        'company_id',
        'author_id',
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

    public function agency_scheme()
    {
        return $this->belongsTo('App\AgencyScheme');
    }
}
