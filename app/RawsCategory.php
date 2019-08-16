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
        'description',
        'seo_description',
        'parent_id',

        'display',
        'system',
        'moderation'
    ];

    // Родитель
    public function parent()
    {
        return $this->belongsTo(RawsCategory::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(RawsCategory::class, 'parent_id');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Аавтор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Сырье
    public function raws()
    {
        return $this->hasMany(Raw::class, 'category_id');
    }

    // Артикулы
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'raws')
        ->where('draft', false)
        ->where('raws.archive', false);
    }

    // Метрики
    public function metrics()
    {
        return $this->belongsToMany(Metric::class, 'preset_metric', 'category_id', 'metric_id');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Производители
    public function manufacturers()
    {
        return $this->morphToMany(Manufacturer::class, 'categories_manufacturer');
    }

    public function groups()
    {
        return $this->morphToMany(ArticlesGroup::class, 'articles_group_entity');
        // ->where('archive', false);
    }
}
