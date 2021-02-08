<?php

namespace App\Models\System\Documents;

use App\Models\System\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Estimate extends BaseModel
{
    use SoftDeletes;

    // use Cachable;

    const ALIAS = 'estimates';
    const DEPENDENCE = true;

//    protected $with = [
//        'client',
//        'payments',
//        'discounts',
//        'goods_items'
//    ];

    protected $dates = [
        'deleted_at',
        'date',
        'registered_at',
        'conducted_at',
    ];

    protected $casts = [
        'date' => 'datetime:d.m.Y',
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

        'registered_at',
        'produced_at',
        'conducted_at',

        'is_dismissed',
        'cancel_ground_id',

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

        'agent_id',
        'agency_scheme_id',
        'share_currency',
        'principal_currency',

        'margin_percent',
        'margin_currency',


        'display',
        'system',
        'moderation',

        // TODO - 24.09.20 - Поля открыты для парсера базы РХ
        'company_id',
        'author_id',
    ];

    public function getDebtAttribute()
    {
        return $this->total - $this->payments->sum('total');
    }

    public function getPaidAttribute()
    {
        return $this->payments->sum('total');
    }

    public function getGoodsItemsReservesAttribute()
    {
        if ($this->goods_items->isNotEmpty()) {
            $count = 0;
            foreach ($this->goods_items as $goodsItem) {
                if (isset($goodsItem->reserve)) {
                    $count += $goodsItem->reserve->count;
                }
            }
            return $count;
        } else {
            return 0;
        }
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
    }


    // Лид
    public function lead()
    {
        return $this->belongsTo('App\Lead');
    }

    // Клиент
    public function client()
    {
        return $this->belongsTo('App\Client');
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
        return $this->morphMany('App\Reserve', 'document');
    }

    // Платежи
    public function payments()
    {
        return $this->morphMany('App\Payment', 'document')
            ->whereNull('canceled_payment_id');
    }

    public function discounts()
    {
        return $this->belongsToMany('App\Discount');
    }

    public function agent()
    {
        return $this->belongsTo('App\Agent')->withArchived();
    }

    public function catalogs_goods()
    {
        return $this->belongsToMany('App\CatalogsGoods', 'catalogs_goods_estimate');
    }

    public function catalogs_services()
    {
        return $this->belongsToMany('App\CatalogsService', 'catalogs_service_estimate');
    }

    public function labels()
    {
        return $this->belongsToMany('App\Label');
    }

    public function cancel_ground()
    {
        return$this->belongsTo('App\EstimatesCancelGround', 'cancel_ground_id');
    }

    public function production()
    {
        return $this->hasOne(Production::class);
    }


    /**
     * Фильтр
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query)
    {
        $filters = $this->getFilters(self::ALIAS);

        if (isset($filters['cities'])) {
            $query->whereHas('lead', function ($q) use ($filters) {
                $q->whereHas('location', function ($q) use ($filters) {
                    $q->whereIn('city_id', $filters['cities']);
                });
            });
        }

        if (isset($filters['status'])) {
            if ($filters['status'] == 'fiz') {
                $query->whereHas('lead', function ($q) {
                    $q->whereNull('company_name');
                });
            }
            if ($filters['status'] == 'ur') {
                $query->whereHas('lead', function ($q) {
                    $q->whereNotNull('company_name');
                });
            }
        }

        if (isset($filters['sources'])) {
            $query->whereHas('lead', function ($q) use ($filters) {
                $q->whereIn('source_id', $filters['sources']);
            });
        }

        if (isset($filters['dismissed'])) {
            $query->where('is_dismissed', $filters['dismissed']);
        }

        if (isset($filters['saled'])) {
            if ($filters['saled'] == 1) {
                $query->whereNotNull('conducted_at');
            } else {
                $query->whereNull('conducted_at');
            }
        }

        if (isset($filters['total_min'])) {
            $query->where('total', '>=', $filters['total_min']);
        }
        if (isset($filters['total_max'])) {
            $query->where('total', '<=', $filters['total_max']);
        }

        if (isset($filters['total_points_min'])) {
            $query->where('total_points', '>=', $filters['total_points_min']);
        }
        if (isset($filters['total_points_max'])) {
            $query->where('total_points', '<=', $filters['total_points_max']);
        }

        if (isset($filters['margin_currency_min'])) {
            $query->where('margin_currency', '>=', $filters['margin_currency_min']);
        }
        if (isset($filters['margin_currency_max'])) {
            $query->where('margin_currency', '<=', $filters['margin_currency_max']);
        }

        if (isset($filters['discount_currency_min'])) {
            $query->where('discount_currency', '>=', $filters['discount_currency_min']);
        }
        if (isset($filters['discount_currency_max'])) {
            $query->where('discount_currency', '<=', $filters['discount_currency_max']);
        }

        if (isset($filters['registered_at_min'])) {
            $query->whereDate('registered_at', '>=', Carbon::createFromFormat('d.m.Y', $filters['registered_at_min']));
        }
        if (isset($filters['registered_at_max'])) {
            $query->whereDate('registered_at', '<=', Carbon::createFromFormat('d . m . Y', $filters['registered_at_max']));
        }

        if (isset($filters['conducted_at_min'])) {
            $query->whereDate('conducted_at', '>=', Carbon::createFromFormat('d.m.Y', $filters['conducted_at_min']));
        }
        if (isset($filters['conducted_at_max'])) {
            $query->whereDate('conducted_at', '<=', Carbon::createFromFormat('d . m . Y', $filters['conducted_at_max']));
        }

        if (isset($filters['filials'])) {
            $query->whereIn('filial_id', $filters['filials']);
        }

        return $query;
    }
}
