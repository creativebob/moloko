<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;
use App\Scopes\Traits\ClientsTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Client extends Model
{

    // Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use ClientsTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

     protected $dates = [
         'first_order_date',
         'last_order_date',
         'deleted_at'
     ];

    protected $fillable = [
        'clientable_id',
        'clientable_type',
    
        'description',
        'discount',
        'points',

        'source_id',
        'loyalty_score',

        'is_vip',
        'is_vip_abc',
        'is_blacklist',

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
        'moderation'
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
        return $this->morphTo();
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
        return $this->hasMany('App\Estimate', 'client_id');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
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


    // Фильтр
    public function scopeFilters($query)
    {

        if (! is_null(request('lost'))) {
            $query->where('is_lost', request('lost'));
        }

        if (! is_null(request('vip'))) {
            $query->where('is_vip', request('vip'));
        }

        if (! is_null(request('blacklist'))) {
            $blacklist = request('blacklist');
            if ($blacklist) {
                $query->has('actual_blacklist');
            }
            if (!$blacklist) {
                $query->doesntHave('actual_blacklist');
            }

        }

        if (request('sources')) {
            $query->whereIn('source_id', request('sources'));
        }

        if (request('loyalties_scores')) {
            $query->whereHas('loyalty_score', function ($q) {
                $q->whereIn('loyalty_score', request('loyalties_scores'));
            });
        }

        if (request('abc')) {
            $query->whereIn('abc', request('abc'));
        }

        if (request('activities')) {
            $query->whereIn('activity', request('activities'));
        }

        if (request('rfm')) {
            $query->whereIn('rfm', request('rfm'));
        }

        if (request('orders_count_min')) {
            $query->where('orders_count', '>=', request('orders_count_min'));
        }
        if (request('orders_count_max')) {
            $query->where('orders_count', '<=', request('orders_count_max'));
        }

        if (request('purchase_frequency_min')) {
            $query->where('purchase_frequency', '>=', request('purchase_frequency_min'));
        }
        if (request('purchase_frequency_max')) {
            $query->where('purchase_frequency', '<=', request('purchase_frequency_max'));
        }

        if (request('customer_equity_min')) {
            $query->where('customer_equity', '>=', request('customer_equity_min'));
        }
        if (request('customer_equity_max')) {
            $query->where('customer_equity', '<=', request('customer_equity_max'));
        }

        if (request('average_order_value_min')) {
            $query->where('average_order_value', '>=', request('average_order_value_min'));
        }
        if (request('average_order_value_max')) {
            $query->where('average_order_value', '<=', request('average_order_value_max'));
        }

        if (request('customer_value_min')) {
            $query->where('customer_value', '>=', request('customer_value_min'));
        }
        if (request('customer_value_max')) {
            $query->where('customer_value', '<=', request('customer_value_max'));
        }

        if (request('ltv_min')) {
            $query->where('ltv', '>=', request('ltv_min'));
        }
        if (request('ltv_max')) {
            $query->where('ltv', '<=', request('ltv_max'));
        }

        if (request('first_order_date_min')) {
            $query->whereDate('first_order_date', '>=', Carbon::createFromFormat('d.m.Y', request()->first_order_date_min));
        }
        if (request('first_order_date_max')) {
            $query->whereDate('first_order_date', '<=', Carbon::createFromFormat('d.m.Y', request()->first_order_date_max));
        }

        if (request('last_order_date_min')) {
            $query->whereDate('last_order_date', '>=', Carbon::createFromFormat('d.m.Y', request()->last_order_date_min));
        }
        if (request('last_order_date_max')) {
            $query->whereDate('last_order_date', '<=', Carbon::createFromFormat('d.m.Y', request()->last_order_date_max));
        }

        if (request('birthday_date_min')) {
            $query->whereHasMorph(
                'clientable',
                [User::class],
                function ($q) {
               $q->whereDate('birthday_date', '>=', Carbon::createFromFormat('d.m.Y', request()->birthday_date_min));
            });
        }
        if (request('birthday_date_max')) {
            $query->whereHasMorph(
                'clientable',
                [User::class],
                function ($q) {
                $q->whereDate('birthday_date', '<=', Carbon::createFromFormat('d.m.Y', request()->birthday_date_max));
            });
        }

        if (! is_null(request('sex'))) {
            $query->whereHasMorph(
                'clientable',
                [User::class],
                function ($q) {
                $q->where('sex', request()->sex);
            });
        }

        if (request('cities')) {
            $query->whereHasMorph(
                'clientable',
                [Company::class, User::class],
                function ($q) {
                $q->whereHas('location', function ($q) {
                    $q->whereHas('city', function ($q) {
                        $q->whereIn('id', request('cities'));
                    });
                });
            });
        }

//        dd($query->toSql());


        return $query;
    }

}
