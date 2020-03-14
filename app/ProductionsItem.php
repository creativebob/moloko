<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ProductionsItem extends Model
{

    // Включаем кеш
//    use Cachable;

    use Commonable;

    protected $dates = ['deleted_at'];
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
    ];

    // Наряд
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function document()
    {
        return $this->belongsTo(Production::class, 'production_id');
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

    // Списания
    public function offs()
    {
        return $this->morphMany(Off::class, 'documents_item');
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

}
