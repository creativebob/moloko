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
        'name',
        'parent_id',
        'category_status',
    ];

    // Получаем компании.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function raws_products()
    {
        return $this->hasMany('App\RawsProduct');
    }

    public function raws_mode()
    {
        return $this->belongsTo('App\RawsMode');
    }

    // Артикул
    public function raws_articles()
    {
        return $this->hasManyThrough('App\RawsArticle', 'App\RawsProduct');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Получаем метрики
    // public function metrics()
    // {
    //     return $this->belongsToMany('App\Metric', 'metric_entity', 'entity_id', 'metric_id')->where('entity', 'raws_categories');
    // }
    public function metrics()
    {
        return $this->morphToMany('App\Metric', 'metric_entity');
    }

    // // Получаем состав
    // public function compositions()
    // {
    //     return $this->belongsToMany('App\RawsProduct', 'compositions', 'raws_category_id', 'composition_id');
    // }

    // --------------------------------------- Запросы -----------------------------------------
    public function getIndex($answer, $request)
    {
        return $this->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->withCount('raws_products')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
    }

    public function getItem($answer, $id)
    {
        return $this->moderatorLimit($answer)->findOrFail($id);
    }
}
