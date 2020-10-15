<?php

namespace App\Models\System\Documents;

use App\Models\System\BaseModel;
use App\Off;
use App\Stock;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Production extends BaseModel
{

    use SoftDeletes;
//    use Cachable;

    protected $dates = [
        'date',
        'produced_at',
        'deleted_at',
    ];

    protected $fillable = [
        'manufacturer_id',
        'stock_id',

        'name',
        'description',

        'date',
        'number',

        'amount',

        'produced_at',

        'draft',

        'display',
        'system',
        'moderation'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    // Поставщик
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Склад
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    // Позиции в наряде
    public function items()
    {
        return $this->hasMany(ProductionsItem::class);
    }

	// Списания
	public function offs()
	{
		return $this->morphMAny(Off::class, 'document');
	}
}
