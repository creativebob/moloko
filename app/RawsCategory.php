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

    // Сырье
    public function raws()
    {
        return $this->hasMany(Raw::class);
    }

    // Режим
    public function mode()
    {
        return $this->belongsTo(RawsMode::class, 'raws_mode_id');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Метрики
    public function one_metrics()
    {
        return $this->morphToMany(Metric::class, 'metric_entity')->where('set_status', 'one');
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
