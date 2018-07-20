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
// use App\Scopes\Filters\DateIntervalFilter;

class Product extends Model
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
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;


    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'article',
        'cost',
        'description',
    ];

    // Получаем компанию
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем категорию
    public function products_group()
    {
        return $this->belongsTo('App\ProductsGroup');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем страну
    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    // Получаем еденицу измерения
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

     // Получаем категорию еденицу измерения
    public function units_category()
    {
        return $this->belongsTo('App\UnitsCategory');
    }

    // Получаем альбом
    public function albums()
    {
        return $this->belongsToMany('App\Album', 'album_entity', 'entity_id', 'album_id')->where('entity', 'product');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    

    // Альбом
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    // Получаем метрики
    public function metrics()
    {
        return $this->belongsToMany('App\Metric', 'metric_entity', 'entity_id', 'metric_id')->where('entity', 'products_categories');
    }

    // Получаем состав
    // public function compositions()
    // {
    //     return $this->belongsToMany('App\Product', 'compositions', 'product_id', 'composition_id');
    // }

    // Получаем артикулы
    // public function articles()
    // {
    //     return $this->hasMany('App\Article');
    // }
}
