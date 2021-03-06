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

class GoodsCategory extends Model
{
    // Включаем кеш
//    use Cachable;

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

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'description',
        'seo_description',
        'parent_id',
        'photo_id',
        'is_direction',

        'display',
        'system',
        'moderation'
    ];

    // Родитель
    public function parent()
    {
        return $this->belongsTo(GoodsCategory::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(GoodsCategory::class, 'parent_id');
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

    // Товары
    public function goods()
    {
        return $this->hasMany(Goods::class, 'category_id');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Артикулы
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'goods')
        ->where('draft', false)
        ->where('goods.archive', false);
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'preset_metric');
    }

    public function raws()
    {
        return $this->belongsToMany(Raw::class, 'preset_raw');
    }

    public function related()
    {
        return $this->belongsToMany(Goods::class, 'preset_related');
    }

    // Производители
    public function manufacturers()
    {
        return $this->morphToMany(Manufacturer::class, 'categories_manufacturer');
    }

    // Направление
    public function direction()
    {
        return $this->morphOne(Direction::class, 'category')
         ->where('archive', false);
    }

    public function groups()
    {
        return $this->morphToMany(ArticlesGroup::class, 'entity', 'articles_group_entity');
    }

    public function getNameWithParentAttribute()
    {
        if($this->parent_id != null){
            return $this->parent->name . ' / ' . $this->name;
        } else {
            return $this->name;
        }
    }
}
