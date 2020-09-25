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

        'number',
        'date',

        'cost',

        'description',

        'draft',

        'is_registered',
        'registered_date',

        'is_produced',

        'is_saled',
        'saled_date',

        'is_dismissed',
        'is_main',
        'is_create_parse',

        'certificate_amount',

        'external',
        'losses_from_points',

        'surplus',
        'points',

        'amount',

        'price_discount',
        'catalogs_item_discount',
        'estimate_discount',
        'client_discount',
        'manual_discount',

        'discount_currency',
        'discount_percent',

        'total',
        'total_points',
        'total_bonuses',

        'margin_percent',
        'margin_currency',



        'display',
        'system',
        'moderation',

        // TODO - 24.09.20 - Поля открыты для парсера базы РХ
        'company_id',
        'author_id',
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

    public function discounts()
    {
        return $this->belongsToMany(Discount::class);
    }

    // Фильтр
    public function scopeFilter($query)
    {

        if (request('client_id')) {
            $query->where('client_id', request('client_id'));
        }

        return $query;
    }
}
