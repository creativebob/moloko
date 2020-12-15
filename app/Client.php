<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Documents\Estimate;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


class Client extends BaseModel
{

    use Notifiable,
        SoftDeletes;

    //    use Cachable;

    const ALIAS = 'clients';
    const DEPENDENCE = false;

    protected $dates = [
        'created_at',
        'first_order_date',
        'last_order_date',
        'deleted_at'
    ];

    protected $fillable = [
        'filial_id',

        'clientable_id',
        'clientable_type',

        'loyalty_id',
        'description',

        'discount',
        'points',

        'source_id',

        'is_vip',
        'is_vip_abc',

        'is_lost',

        'activity',
        'abc',
        'xyz',
        'abcxyz',
        'rfm',

        'first_order_date',
        'last_order_date',

        'orders_count',
        'lifetime',
        'purchase_frequency',
        'ait',
        'customer_equity',
        'average_order_value',
        'customer_value',
        'ltv',
        'use_promo_count',
        'promo_rate',

        'display',
        'system',
        'moderation',

        'created_at'
    ];

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем лояльность
    public function loyalty()
    {
        return $this->belongsTo('App\Loyalty', 'loyalty_id');
    }

    // Основной
    public function main_phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity')->wherePivot('main', '=', 1)->whereNull('archive')->withPivot('archive');
    }

    // Получаем компанию или пользователя
    public function clientable()
    {
        return $this->morphTo()
//            ->withDefault([
//                'name' => 'Имя не указано'
//            ])
            ;
    }


    // // Получаем комментарии
    // public function agent()
    // {
    //     return $this->morphOne(Company::class, 'clientable');
    // }

    // // Получаем компанию
    public function company()
    {
        return $this->belongsTo('App\Company', 'clientable_id');
    }

    // Получаем заказы
    public function orders()
    {
        return $this->hasMany('App\Models\System\Documents\Estimate', 'client_id');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class)
            ->where('is_dismissed', false);
    }

    // Получаем лиды
    public function leads()
    {
        return $this->hasMany('App\Lead', 'client_id');
    }

    // Получаем источник
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    // Получаем клиента
    public function contract()
    {
        return $this->hasOne(ContractsClient::class);
    }

    // Пользовательские оценки
    public function loyalties_scores()
    {
        return $this->hasMany(ClientsLoyaltiesScore::class);
    }

    public function loyalty_score()
    {
        return $this->hasOne(ClientsLoyaltiesScore::class)
            ->latest();
    }

    // Чёрный список
    public function blacklists()
    {
        return $this->hasMany(ClientsBlacklist::class);
    }

    public function actual_blacklist()
    {
        return $this->hasOne(ClientsBlacklist::class)
            ->whereNull('end_date');
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

        if (isset($filters['lost'])) {
            $query->where('is_lost', $filters['lost']);
        }

        if (isset($filters['vip'])) {
            $query->where('is_vip', $filters['vip']);
        }

        if (isset($filters['blacklist'])) {
            $blacklist = $filters['blacklist'];
            if ($blacklist) {
                $query->has('actual_blacklist');
            }
            if (!$blacklist) {
                $query->doesntHave('actual_blacklist');
            }
        }

        if (isset($filters['sources'])) {
            $query->whereIn('source_id', $filters['sources']);
        }

        if (isset($filters['loyalties_scores'])) {
            $query->whereHas('loyalty_score', function ($q) use ($filters) {
                $q->whereIn('loyalty_score', $filters['loyalties_scores']);
            });
        }

        if (isset($filters['abc'])) {
            $query->whereIn('abc', $filters['abc']);
        }

        if (isset($filters['activities'])) {
            $query->whereIn('activity', $filters['activities']);
        }

        if (isset($filters['rfm'])) {
            $query->whereIn('rfm', $filters['rfm']);
        }

        if (isset($filters['orders_count_min'])) {
            $query->where('orders_count', '>=', $filters['orders_count_min']);
        }
        if (isset($filters['orders_count_max'])) {
            $query->where('orders_count', '<=', $filters['orders_count_max']);
        }

        if (isset($filters['purchase_frequency_min'])) {
            $query->where('purchase_frequency', '>=', $filters['purchase_frequency_min']);
        }
        if (isset($filters['purchase_frequency_max'])) {
            $query->where('purchase_frequency', '<=', $filters['purchase_frequency_max']);
        }

        if (isset($filters['customer_equity_min'])) {
            $query->where('customer_equity', '>=', $filters['customer_equity_min']);
        }
        if (isset($filters['customer_equity_max'])) {
            $query->where('customer_equity', '<=', $filters['customer_equity_max']);
        }

        if (isset($filters['average_order_value_min'])) {
            $query->where('average_order_value', '>=', $filters['average_order_value_min']);
        }
        if (isset($filters['average_order_value_max'])) {
            $query->where('average_order_value', '<=', $filters['average_order_value_max']);
        }

        if (isset($filters['customer_value_min'])) {
            $query->where('customer_value', '>=', $filters['customer_value_min']);
        }
        if (isset($filters['customer_value_max'])) {
            $query->where('customer_value', '<=', $filters['customer_value_max']);
        }

        if (isset($filters['discount_min'])) {
            $query->where('discount', '>=', $filters['discount_min']);
        }
        if (isset($filters['discount_max'])) {
            $query->where('discount', '<=', $filters['discount_max']);
        }

        if (isset($filters['points_min'])) {
            $query->where('points', '>=', $filters['points_min']);
        }
        if (isset($filters['points_max'])) {
            $query->where('points', '<=', $filters['points_max']);
        }

//        if ($filters['ltv_min']) {
//            $query->where('ltv', '>=', $filters['ltv_min']);
//        }
//        if ($filters['ltv_max']) {
//            $query->where('ltv', '<=', $filters['ltv_max']);
//        }

        if (isset($filters['first_order_date_min'])) {
            $query->whereDate('first_order_date', '>=', Carbon::createFromFormat('d.m.Y', $filters['first_order_date_min']));
        }
        if (isset($filters['first_order_date_max'])) {
            $query->whereDate('first_order_date', '<=', Carbon::createFromFormat('d.m.Y', $filters['first_order_date_max']));
        }

        if (isset($filters['last_order_date_min'])) {
            $query->whereDate('last_order_date', '>=', Carbon::createFromFormat('d.m.Y', $filters['last_order_date_min']));
        }
        if (isset($filters['last_order_date_max'])) {
            $query->whereDate('last_order_date', '<=', Carbon::createFromFormat('d.m.Y', $filters['last_order_date_max']));
        }

        if (isset($filters['birthday_date_min'])) {
            $query->whereHasMorph(
                'clientable',
                [User::class],
                function ($q) use ($filters) {
                    $q->whereDate('birthday_date', '>=', Carbon::createFromFormat('d.m.Y', $filters['birthday_date_min']));
                });
        }
        if (isset($filters['birthday_date_max'])) {
            $query->whereHasMorph(
                'clientable',
                [User::class],
                function ($q) use ($filters) {
                    $q->whereDate('birthday_date', '<=', Carbon::createFromFormat('d.m.Y', $filters['birthday_date_max']));
                });
        }

        if (isset($filters['estimate_date_min'])) {
            $query->whereHas('estimates', function ($q) use ($filters) {
                $q->whereDate('conducted_at', '>=', Carbon::createFromFormat('d.m.Y', $filters['estimate_date_min']));
            });
        }
        if (isset($filters['estimate_date_max'])) {
            $query->whereHas('estimates', function ($q) use ($filters) {
                $q->whereDate('conducted_at', '<=', Carbon::createFromFormat('d.m.Y', $filters['estimate_date_max']));
            });
        }

        if (isset($filters['gender'])) {
            $query->whereHasMorph(
                'clientable',
                [
                    User::class
                ],
                function ($q) use ($filters) {
                    $q->where('gender', $filters['gender']);
                });
        }

        if (isset($filters['cities'])) {
            $query->whereHasMorph(
                'clientable',
                [
                    Company::class,
                    User::class
                ],
                function ($q) use ($filters) {
                    $q->whereHas('location', function ($q) use ($filters) {
                        $q->whereHas('city', function ($q) use ($filters) {
                            $q->whereIn('id', $filters['cities']);
                        });
                    });
                });
        }

        if (isset($filters['filials'])) {
            $query->whereIn('filial_id', $filters['filials']);
        }

        return $query;
    }
}
