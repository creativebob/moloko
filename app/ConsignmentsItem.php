<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


class ConsignmentsItem extends Model
{

    // Включаем кеш
//    use Cachable;

    use Commonable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'consignment_id',
        'cmv_id',
        'cmv_type',
        'count',
        'cost',
	    'amount',

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
