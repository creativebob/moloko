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
        'manufacturer_id',
        'metrics_count',
        'compositions_count',
        'author_id',
        'editor_id',
    ];

    // Продукт
    public function product()
    {
        return $this->belongsTo('App\GoodsProduct', 'goods_product_id');
    }

    // Товар
    public function goods()
    {
        return $this->hasMany('App\Goods');
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

    // Производитель
    public function manufacturer()
    {
        return $this->belongsTo('App\Company');
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

}
