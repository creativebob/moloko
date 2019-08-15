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

    // protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id',
        'client_id',

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




}