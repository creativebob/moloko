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

    // Общая база
    // protected $connection = 'general';
    //
    protected $dates = ['deleted_at'];

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'region_id',
        'area_id',
        'country_id',
        'code',
        'vk_external_id',
        'display',
        'system',
        'author_id'
    ];

    // Район
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    // Область
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Страна
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Локации
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
