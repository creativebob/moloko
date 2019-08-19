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

class ExpendablesCategory extends Model
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
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'seo_description',
        'parent_id',
    ];

    // Родитель
    public function parent()
    {
        return $this->belongsTo(RawsCategory::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(ExpendablesCategory::class, 'parent_id');
    }

    // Компании
    public function company()
    {
    	return $this->belongsTo(Company::class);
    }

    // Аавтор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'preset_metric');
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
