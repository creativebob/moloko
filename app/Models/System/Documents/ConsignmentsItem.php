<?php

namespace App\Models\System\Documents;

use App\Currency;
use App\Entity;
use App\Manufacturer;
use App\Models\System\BaseModel;
use App\Receipt;
use App\Stock;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ConsignmentsItem extends BaseModel
{
//    use Cachable;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'consignment_id',
        'cmv_id',
        'cmv_type',
        'count',
        'cost',
        'amount',

        'stock_id',

        'currency_id',

        'entity_id',
        'manufacturer_id',

        'vat_rate',
        'description',
        'total',
        'amount',
        'stock_id',
    ];

    // Родительская смета
    public function consignment()
    {
        return $this->belongsTo(Consignment::class);
    }

    public function document()
    {
        return $this->belongsTo(Consignment::class, 'consignment_id');
    }

    // Склад
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    //
    public function cmv()
    {
        return $this->morphTo();
    }

    // сущность
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    // Поступления
    public function receipt()
    {
        return $this->morphOne(Receipt::class, 'documents_item');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    // Валюта
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
