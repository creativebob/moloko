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

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class Location extends Model
{

    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    use DateIntervalFilter;

    protected $with = [
        'city',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'country_id',
        'city_id',
        'address',
        'zip_code',
        'author_id',
        'latitude',
        'longitude',

        'display',
        'system',
        'moderation'
    ];

    // Получаем город.
    public function city()
    {
        return $this->belongsTo('App\City');
    }

    // Получаем страну.
    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    // Получаем компании.
    // public function companies()
    // {
    //     return $this->hasMany('App\Company');
    // }
}
