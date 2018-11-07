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

use App\Scopes\Traits\ManufacturersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class GoodsArticle extends Model
{
    // Включаем кеш
    // use Cachable;

    use Notifiable;
    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    use ManufacturersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $fillable = [
        'company_id',
        'goods_product_id',
        'name',
        'description',
        'internal',
        'metrics_count',
        'compositions_count',
        'author_id',
        'editor_id',
    ];

    // Метрики
    // public function metrics_values()
    // {
    //     return $this->belongsToMany('App\Metric', 'goods_values', 'goods_id', 'entity_id')->where('entity', 'metrics')->withPivot('entity', 'value');
    // }

    public function metrics_values()
    {
        return $this->morphedByMany('App\Metric', 'goods_values')->withPivot('value');
    }

    // Состав
    // public function compositions_values()
    // {
    //     return $this->belongsToMany('App\Article', 'article_values', 'article_id', 'entity_id')->where('entity', 'articles')->withPivot('entity', 'value');
    // }

    public function raws_compositions_values()
    {
        return $this->morphedByMany('App\Raw', 'goods_values')->withPivot('value');
    }

    // public function compositions_values()
    // {
    //     return $this->belongsToMany('App\Product', 'article_values', 'article_id', 'entity_id')->where('entity', 'compositions')->withPivot('entity', 'value');
    // }

    // public function compositions()
    // {
    //     return $this->belongsToMany('App\Product', 'compositions', 'article_id', 'entity_id')->where('entity', 'compositions')->withPivot('entity', 'value');
    // }

     // Продукт
    public function goods_product()
    {
        return $this->belongsTo('App\GoodsProduct');
    }

    // Производитель
    // public function manufacturer()
    // {
    //     return $this->belongsTo('App\Company', 'manufacturer_id');
    // }

    //  // Продукт
    // public function metrics_list($metrics_list)
    // {
    //     return $this->belongsToMany('App\Metric', 'article_values', 'article_id', 'entity_id')->where('entity', 'metrics')->wherePivotIn('entity_id', $metrics_list);
    // }

    // Продукт
    public function goods()
    {
        return $this->hasMany('App\Goods');
    }
    
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем альбом
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }


    // Метрики
    public function metrics()
    {
        return $this->morphedByMany('App\Metric', 'goods_articles_values')->withPivot('value');
    }

    // Состав (сырье)
    public function compositions()
    {
        return $this->morphedByMany('App\RawsArticle', 'goods_articles_values')->withPivot('value');
    }

    // Состав (набор)
    public function set_compositions()
    {
        return $this->morphedByMany('App\GoodsArticle', 'goods_articles_values')->withPivot('value');
    }
}
