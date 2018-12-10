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
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class RawsCategory extends Model
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

    // Фильтры
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'seo_description',
        'photo_id',
        'parent_id',
        'raws_mode_id',
        'category_id',
        'author_id',
        'editor_id',
    ];

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Группы
    public function raws_products()
    {
        return $this->hasMany('App\RawsProduct');
    }

    // Режим
    public function raws_mode()
    {
        return $this->belongsTo('App\RawsMode');
    }

    // Артикул
    public function raws_articles()
    {
        return $this->hasManyThrough('App\RawsArticle', 'App\RawsProduct');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany('App\Metric', 'metric_entity');
    }
}
