<?php

namespace App;

use App\Models\Traits\Cmvable;
use App\Models\Traits\Commonable;
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

    use Commonable;
    use Cmvable;

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

    // Категория
    public function category()
    {
        return $this->belongsTo(GoodsCategory::class);
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(GoodsStock::class, 'cmv_id');
    }

    public function getRestAttribute()
    {
        if(!empty($this->hasMany(GoodsStock::class, 'cmv_id')->where('filial_id', 1)->first())){
            return $this->hasMany(GoodsStock::class, 'cmv_id')->where('filial_id', 1)->first()->free;  
        } else {
            return null;
        }
    }

    // Пункты каталога
    public function catalogs_items()
    {
        return $this->belongsToMany(CatalogsGoodsItem::class, 'prices_goods', 'goods_id', 'catalogs_goods_item_id');
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
}
