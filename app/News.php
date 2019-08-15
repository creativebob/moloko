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

use Carbon\Carbon;

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
        'slug',
        'preview',
        'publish_begin_date',
        'publish_end_date',
        'content',
        'rubricator_id',
        'rubricators_item_id',

        'display',
        'system',
        'moderation'
    ];

    public function setPublishBeginDateAttribute($value)
    {
        $this->attributes['publish_begin_date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function setPublishEndDateAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['publish_end_date'] = Carbon::createFromFormat('d.m.Y', $value);
        }
    }


    // // Cайт
    // public function site()
    // {
    //     return $this->belongsTo('App\Site');
    // }

    // Rомпания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Превью
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Альбом
    public function albums()
    {
        return $this->morphToMany(Album::class, 'album_entity');
    }

    // // Города
    // public function cities()
    // {
    //     return $this->morphToMany(City::class, 'city_entity');
    // }

}
