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

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'estimate_id',
        'currency_id',

        'price_id',
        'goods_id',
        'stock_id',

        'company_id',
        'author_id',

        'count',
        'price',

        'cost',
        'margin_percent',
        'margin_currency',

        'amount',
        'total',

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
