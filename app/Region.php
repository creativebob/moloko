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

class Region extends Model
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

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'code',
        'vk_external_id',
        'display',
        'country_id',
        'system',
        'author_id',

        'display',
        'system',
        'moderation'
    ];

    // Районы
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    // Города
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // Города без района
    public function cities_without_area()
    {
        return $this->hasMany(City::class)
            ->whereNull('area_id');
    }
}
