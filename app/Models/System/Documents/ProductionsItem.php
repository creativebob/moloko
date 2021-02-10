<?php

namespace App\Models\System\Documents;

use App\Entity;
use App\Manufacturer;
use App\Models\System\BaseModel;
use App\Off;
use App\Receipt;
use App\Stock;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionsItem extends BaseModel
{

    use SoftDeletes;
//    use Cachable;

    protected $dates = [
        'dismissed_at',
        'deleted_at'
    ];

    protected $fillable = [
        'production_id',

        'cmv_id',
        'cmv_type',

        'cost',
        'count',
	    'amount',

        'entity_id',
        'manufacturer_id',

        'description',
        'stock_id',
        'estimates_goods_item_id',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function document()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function cmv()
    {
        return $this->morphTo();
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function offs()
    {
        return $this->morphMany(Off::class, 'documents_item');
    }

    public function receipt()
    {
        return $this->morphOne(Receipt::class, 'documents_item');
    }

    public function receipts()
    {
        return $this->morphMany(Receipt::class, 'documents_item');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function estimates_goods_item()
    {
        return $this->belongsTo(EstimatesGoodsItem::class);
    }

}
