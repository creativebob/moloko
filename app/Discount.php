<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;

    protected $dates  = [
        'begined_at',
        'ended_at',
    ];

    protected $fillable = [
        'name',
        'description',

        'percent',
        'currency',
        'discount_mode',

        'is_block',

        'filial_id',

        'begined_at',
        'ended_at',

        'archive',

        'display',
        'system',
        'moderation'
    ];

    public function getBeginAttribute()
    {
        return $this->begined_at->format('d.m.Y H:i');
    }

    public function getEndAttribute()
    {
        $value = null;
        if ($this->ended_at) {
            $value = $this->ended_at->format('d.m.Y H:i');
        }
        return $value;
    }

    public function prices_goods()
    {
        return $this->belongsToMany(PricesGoods::class, 'discount_price_goods', 'discount_id', 'price_goods_id');
    }

    public function catalogs_goods_items()
    {
        return $this->belongsToMany(CatalogsGoodsItem::class, 'discount_catalogs_goods_item', 'discount_id', 'catalogs_goods_item_id');
    }


}
