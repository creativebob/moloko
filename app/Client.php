<?php

namespace App;

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

        'source_id',
        'loyalty_score',
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



}
