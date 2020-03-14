<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Estimate extends Model
{

    // Включаем кеш
    // use Cachable;

    use SoftDeletes;

    use Commonable;

    protected $dates = [
        'deleted_at',
        'date'
    ];
    protected $fillable = [
        'lead_id',
        'client_id',
	    'filial_id',

        'company_id',

        'number',
        'date',
        'description',
        'author_id',
        'draft',

        'is_registered',
        'is_produced',
        'is_saled',

        'amount',
        'total',
        'discount',
        'discount_percent',

        'display',
        'system',
        'moderation'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
    }


    // Лид
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    // Клиент
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Товары
    public function goods_items()
    {
        return $this->hasMany(EstimatesGoodsItem::class);
    }

    // Услуги
    public function services_items()
    {
        return $this->hasMany(EstimatesServicesItem::class);
    }

    // Резервы
    public function reserves()
    {
        return $this->morphMany(Reserve::class, 'document');
    }

    // Платежи
    public function payments()
    {
        return $this->morphMany(Payment::class, 'document');
    }
}
