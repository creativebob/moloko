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
        'company_id',
        'name',
        'description',
        'seo_description',
        'photo_id',
        'parent_id',
        'goods_mode_id',
        'category_id',
        'author_id',
        'editor_id',
    ];

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

    // Товары
    public function goods()
    {
        return $this->hasMany(Goods::class, 'category_id');
    }

    // Режим
    public function mode()
    {
        return $this->belongsTo(GoodsMode::class, 'goods_mode_id');
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
        return $this->belongsToMany(Metric::class, 'preset_metric', 'category_id', 'metric_id');
    }

    // Состав
    // public function compositions()
    // {
    //     return $this->belongsToMany(Article::class, 'preset_composition', 'category_id', 'composition_id');
    // }

    public function compositions()
    {
        return $this->belongsToMany(Raw::class, 'preset_composition');
    }

    // Один
    // public function one_metrics()
    // {
    //     return $this->morphToMany(Metric::class, 'metric_entity')->where('set_status', 'one');
    // }

    // // Набор
    // public function set_metrics()
    // {
    //     return $this->morphToMany(Metric::class, 'metric_entity')->where('set_status', 'set');
    // }

    // Состав (только сырье)
    // public function compositions()
    // {
    //     return $this->morphedByMany(RawsArticle::class, 'compositions');
    // }

    // Производители
    public function manufacturers()
    {
        return $this->morphToMany(Manufacturer::class, 'categories_manufacturer');
    }

    // Направление
    public function direction()
    {
        return $this->morphOne(Direction::class, 'category');
        // ->where('archive', false);
    }

    public function groups()
    {
        return $this->morphToMany(ArticlesGroup::class, 'articles_group_entity');
        // ->where('archive', false);
    }

}
