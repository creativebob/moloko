<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
    

// Фильтры
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class City extends Model
{

    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    // Включаем Scopes
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    // Фильтры
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];
    protected $table = 'cities';
    protected $fillable = [
        'city_name',
        'region_id',
        'area_id',
        'city_code',
        'city_vk_external_id',
    ];

    // Получаем район данного города.
    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    // Получаем область данного города.
    public function region()
    {
        return $this->belongsTo('App\Region');
    }

    // Получаем локации города.
    public function locations()
    {
        return $this->hasMany('App\Location');
    }

    // Получаем филиалы и отделы города.
    public function departments()
    {
        return $this->hasMany('App\Department');
    }

    // Получаем пользователей.
    public function users()
    {
        return $this->hasMany('App\User');
    }

}
