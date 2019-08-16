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

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class PricesGoods extends Model
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

    protected $table = 'prices_goods';

    protected $fillable = [
        'catalogs_goods_item_id',
        'catalogs_goods_id',
        'goods_id',
        'filial_id',
        'price',
        'archive',

        'display',
        'system',
        'moderation'
    ];


    // Каталог
    public function catalog()
    {
        return $this->belongsTo(CatalogsGoods::class, 'catalogs_goods_id');
    }

    // Пункты каталога
    public function catalogs_item()
    {
        return $this->belongsTo(CatalogsGoodsItem::class, 'catalogs_goods_item_id');
    }

    // Филиал
    public function filial()
    {
        return $this->belongsTo(Department::class);
    }

    // Товар
    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    // История
    public function history()
    {
        return $this->hasMany(PricesGoodsHistory::class, 'prices_goods_id');
    }

    // Актуальная цена
    public function actual_price()
    {
        return $this->hasOne(PricesGoodsHistory::class, 'prices_goods_id')
            ->whereNull('end_date');
    }

    // Предок
    public function ancestor()
    {
        return $this->belongsTo(PricesGoods::class);
    }

    // Последователь
    public function follower()
    {
        return $this->hasOne(PricesGoods::class, 'ancestor_id')
            ->where('archive', false);
    }

    // Общее отношение для товаров и услуг
    public function product()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }
}
