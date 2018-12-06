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
use App\Scopes\Traits\SuppliersTraitScopes;

// use Illuminate\Support\Facades\Auth;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class Goods extends Model
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
    use SuppliersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $fillable = [
        'company_id',
        'goods_article_id',
        'description',
        'manually',
        'external',
        'manufacturer_id',
        'cost',
        'price',
        'album_id',
        'photo_id',

        'portion_status',
        'portion_name',
        'portion_abbreviation',
        'portion_count',

        'author_id',
        'editor_id',
    ];

    public function goods_article()
    {
        return $this->belongsTo('App\GoodsArticle');
    }

    // Получаем компанию.
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

    // Каталоги
    public function catalogs()
    {
        return $this->morphToMany('App\Catalog', 'catalog_products');
    }

    // // Метрики
    // public function metrics()
    // {
    //     return $this->morphedByMany('App\Metric', 'goods_values')->withPivot('value');
    // }

    // // Состав
    // // public function compositions_values()
    // // {
    // //     return $this->belongsToMany('App\Article', 'article_values', 'article_id', 'entity_id')->where('entity', 'articles')->withPivot('entity', 'value');
    // // }

    // // Состав (сырье)
    // public function compositions()
    // {
    //     return $this->morphedByMany('App\RawsArticle', 'goods_values')->withPivot('value');
    // }

    // // Состав (набор)
    // public function set_compositions()
    // {
    //     return $this->morphedByMany('App\GoodsArticle', 'goods_values')->withPivot('value');
    // }

    // public function getCompositions1Attribute() {

    //     if ($this->goods_article->goods_product->status == 'one') {
    //         return $this->morphedByMany('App\RawsArticle', 'goods_values')->withPivot('value');
    //     } else {
    //         return $this->morphedByMany('App\GoodsArticle', 'goods_values')->withPivot('value');
    //     }
    // }

    // public function compositions_values()
    // {
    //     return $this->belongsToMany('App\Product', 'article_values', 'article_id', 'entity_id')->where('entity', 'compositions')->withPivot('entity', 'value');
    // }

    // public function compositions()
    // {
    //     return $this->belongsToMany('App\Product', 'compositions', 'article_id', 'entity_id')->where('entity', 'compositions')->withPivot('entity', 'value');
    // }


    // Получаем составы заказов
    public function order_compositions()
    {
        return $this->morphMany('App\OrderComposition', 'order_compositions');
    }

}
