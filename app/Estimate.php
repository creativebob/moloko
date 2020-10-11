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
        'currency_id',

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
        return $this->hasMany(EstimatesGoodsItem::class)
            ->oldest('sort');
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


        // if (request('cities')) {
        //     $query->where(
        //         'client', 
        //         function ($q) {
        //             $query->whereHasMorph(
        //                 'clientable',
        //                 [
        //                     Company::class,
        //                     User::class
        //                 ],
        //                 function ($q) {
        //                 $q->whereHas('location', function ($q) {
        //                     $q->whereHas('city', function ($q) {
        //                         $q->whereIn('id', request('cities'));
        //                     });
        //                 });
        //             });
        //         }
        //     );
        // }


        if (! is_null(request('dismissed'))) {
            $query->where('is_dismissed', request('dismissed'));
        }

        if (! is_null(request('saled'))) {
            $query->where('is_saled', request('saled'));
        }

        if (request('total_min')) {
            $query->where('total', '>=', request()->total_min);
        }

        if (request('total_max')) {
            $query->where('total', '<=', request()->total_max);
        }

        if (request('total_points_min')) {
            $query->where('total_points', '>=', request()->total_points_min);
        }

        if (request('total_points_max')) {
            $query->where('total_points', '<=', request()->total_points_max);
        }

        if (request('margin_currency_min')) {
            $query->where('margin_currency', '>=', request()->margin_currency_min);
        }

        if (request('margin_currency_max')) {
            $query->where('margin_currency', '<=', request()->margin_currency_max);
        }

        if (request('discount_currency_min')) {
            $query->where('discount_currency', '>=', request()->discount_currency_min);
        }

        if (request('discount_currency_max')) {
            $query->where('discount_currency', '<=', request()->discount_currency_max);
        }


        // TODO - 01.07.20 - Фильтруем по дате регистрации, нужно фильтровать по дате продажи
        if (request('registered_date_min')) {
            $query->whereDate('registered_date', '>=', Carbon::createFromFormat('d.m.Y', request()->registered_date_min));
        }

        if (request('registered_date_max')) {
            $query->whereDate('registered_date', '<=', Carbon::createFromFormat('d.m.Y', request()->registered_date_max));
        }




        return $query;
    }
}
