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
        'date',
        'registered_date',
    ];

    protected $fillable = [
        'lead_id',
        'client_id',
	    'filial_id',

        'company_id',

        'number',
        'cost',

        'date',
        'description',
        'author_id',
        'draft',

        'is_registered',
        'registered_date',

        'is_produced',
        'is_saled',
        'is_dismissed',
        'is_main',
        'is_create_parse',

        'certificate_amount',

        'external',
        'losses_from_points',

        'surplus',
        'points',

        'discount_items_currency',

        'amount',
        'total',

        'margin_percent',
        'margin_currency',

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

    // Фильтр
    public function scopeFilters($query)
    {

        if (request('client_id')) {
            $query->where('client_id', request('client_id'));
        }

        return $query;
    }
}
