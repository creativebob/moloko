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

class News extends Model
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

    protected $dates = [
        'deleted_at',
        'publish_begin_date',
        'publish_end_date'
    ];

    protected $fillable = [
        'name',
        'alias',
        'title',
        'preview',
        'publish_begin_date',
        'publish_end_date',
        'content',
        'site_id',
        'photo_id',
        'company_id',
    ];

    // Cайт
    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    // Rомпания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Превью
    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Автор
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Альбом
    public function albums()
    {
        return $this->morphToMany('App\Album', 'album_entity');
    }

    // Города
    public function cities()
    {
        return $this->morphToMany('App\City', 'city_entities');
    }

}
