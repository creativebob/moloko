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

    // Артикул товара
    public function article()
    {
        return $this->belongsTo('App\GoodsArticle', 'goods_article_id');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Автор
    public function author()
    {
        return $this->belongsTo('App\User');
    }

    // Альбом
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Каталоги
    public function catalogs()
    {
        return $this->morphToMany('App\Catalog', 'catalog_products');
    }

    // Рабочие процессы
    public function workflows()
    {
        return $this->morphMany('App\Workflow', 'workflows');
    }

    // Составы заказов
    public function order_compositions()
    {
        return $this->morphMany('App\OrderComposition', 'order_compositions');
    }

}
