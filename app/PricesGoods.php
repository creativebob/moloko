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
        
        'discount_mode',
        'discount_percent',
        'discount_currency',
        
        'price_discount_id',
        'price_discount',
        'total_price_discount',
        
        'catalogs_item_discount_id',
        'catalogs_item_discount',
        'total_catalogs_item_discount',
        
        'total',

        'points',
        'currency_id',

        'archive',

        'status',
        'is_hit',
        'is_new',
        
        'is_show_price',

        'is_discount',

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

    public function catalogs_item_public()
    {
        return $this->belongsTo(CatalogsGoodsItem::class, 'catalogs_goods_item_id')
            ->where('display', true);
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

    public function goods_public()
    {
        return $this->belongsTo(Goods::class, 'goods_id')
            ->with('article')
            ->whereHas('article', function ($q) {
                $q->with([
                    'raws'
                ])
                    ->where([
                        'draft' => false
                    ]);
            })
            ->where([
                'display' => true,
                'archive' => false,
            ]);
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

    // Валюта
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function scopePublic($query)
    {
        $query->where([
            'display' => true,
            'archive' => false
        ]);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'like_prices_goods', 'prices_goods_id', 'user_id');
    }

    public function promotions()
    {
        return $this->belongsToMany(PricesGoods::class, 'promotion_price_goods', 'price_goods_id', 'promotion_id');
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_price_goods', 'price_goods_id', 'discount_id');
    }

    public function discounts_actual()
    {
        return $this->belongsToMany(Discount::class, 'discount_price_goods', 'price_goods_id', 'discount_id')
            ->where('archive', false)
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->withPivot([
                'sort'
            ]);
    }
    
    public function discount_price()
    {
        return $this->belongsTo(Discount::class, 'price_discount_id');
    }
    
    public function discount_catalogs_item()
    {
        return $this->belongsTo(Discount::class, 'catalogs_item_discount_id');
    }

    // Фильтр
    public function scopeFilter($query)
    {
        if (request('price')) {
            $price = request('price');
            if (isset($price['min'])) {
                $query->where('price', '>=', $price['min']);
            }
            if (isset($price['max'])) {
                $query->where('price', '<=', $price['max']);
            }
            $query->orderBy('price');
        }

        if (request('weight')) {
            $weight = request('weight');
            $query->whereHas('goods_public', function ($q) use ($weight) {
                $q->whereHas('article', function ($q) use ($weight) {
                    $q->where('weight', '>=', $weight['min'] / 1000)
                        ->where('weight', '<=', $weight['max'] / 1000);
                });
            });
        }

        if (request('catalogs_goods_item')) {
            $catalogs_goods_item = request('catalogs_goods_item');
            $query->where('catalogs_goods_item_id', $catalogs_goods_item);
        }

        if (request('raws_articles_groups')) {
            $raws_articles_groups = request('raws_articles_groups');
//		    dd($raws_articles_groups);

            $query->whereHas('goods_public', function ($q) use ($raws_articles_groups) {
                $q->whereHas('article', function ($q) use ($raws_articles_groups) {
                    foreach ($raws_articles_groups as $item) {
                        $q->whereHas('attachments', function ($q) use ($item) {
                            $q->whereHas('article', function ($q) use ($item) {
                                $q->where('articles_group_id', $item);
                            });
                        });
                    }
                });
            });
        }

        return $query;
    }


//    public function scopeFilter(Builder $builder, QueryFilter $filters)
//    {
//        return $filters->apply($builder);
//    }

    /**
     * Вычисление итоговой стоимости товара с учетом подключенных скидок.
     * Сложение скидок прайса, раздела каталога.
     *
     * @return int|mixed
     */
    public function getTotalWithDiscountsAttribute()
    {
        $total = 0;
        $res = [];

        // Вычисление всех скидок на прайсе товара
        if ($this->is_discount == 1) {
            $priceGoodsDiscounts = $this->discounts_actual;
            $totalWithoutDiscounts = $this->total;
            $resPrice = $this->getDynamicDiscounts($priceGoodsDiscounts, $totalWithoutDiscounts);
            $total = $resPrice['total'];
        }

        // Вычисление всех скидок на разделе каталога, в котором находится прайс
        if (! $resPrice['break']) {
            $catalogsGoodsItem = $this->catalogs_item;
            if ($catalogsGoodsItem->is_discount == 1) {
                $catalogsGoodsItemDiscounts = $catalogsGoodsItem->discounts_actual;
                $resCatalogItem = $this->getDynamicDiscounts($catalogsGoodsItemDiscounts, $total);
                $total = $resCatalogItem['total'];
            }
        }

        return $total;
    }

    public function getDynamicDiscounts($discounts, $totalWithoutDiscounts)
    {
        $break = false;
        $sumPercent = 0;
        $sumCurrency = 0;
        foreach ($discounts as $discount) {
            switch ($discount->mode) {
                case(1):
                    $sumPercent += $discount->percent;
                    break;
                case(2):
                    $sumCurrency += $discount->currency;
                    break;
            }
            if ($discount->is_block == 1) {
                $break = true;
                break;
            }
        }

        $sumDiscountInCurrency = $totalWithoutDiscounts / 100 * $sumPercent;
        $total = $totalWithoutDiscounts - $sumDiscountInCurrency - $sumCurrency;

        $res = [
            'total' => $total,
            'break' => $break
        ];
        return $res;
    }
}
