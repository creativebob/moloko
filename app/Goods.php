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
        'article_id',
        'category_id',
        'price_unit_category_id',
        'price_unit_id',

        'archive',
        'serial',

        'display',
        'system',
        'moderation'
    ];

    // Артикул
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function core()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    // Категория
    public function category()
    {
        return $this->belongsTo(GoodsCategory::class);
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'entity_metric_value')
        ->withPivot('value');
    }

    // Пункты каталога
    public function catalogs_items()
    {
        return $this->belongsToMany(CatalogsGoodsItem::class, 'prices_goods', 'goods_id', 'catalogs_goods_item_id');
    }

    // Рабочие процессы
    public function workflows()
    {
        return $this->morphMany(Workflow::class, 'workflows');
    }

    // Составы заказов
    public function order_compositions()
    {
        return $this->morphMany(OrderComposition::class, 'order_compositions');
    }

    // Пункты каталога
    public function prices()
    {
        return $this->hasMany(PricesGoods::class)
            ->where('archive', false);
    }

    // 
    public function price_unit()
    {
        return $this->belongsTo(Unit::class, 'price_unit_id');
    }

    // Себестоимость
    public function cost()
    {
        return $this->morphOne(Cost::class, 'cmv');
    }

    // Склад
    public function stock()
    {
        return $this->hasOne(GoodsStock::class, 'cmv_id');
    }

    public function getCostAverageAttribute()
    {
        if($this->article->manufacturer_id){
            return $this->morphMany(Cost::class, 'cmv')->where('manufacturer_id', $this->article->manufacturer_id)->first()->average;
        } else {
            return 0;
        } 
    }

}
