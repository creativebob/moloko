<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class PricesService extends BaseModel
{
    use Cachable,
        SoftDeletes;

    protected $fillable = [
        'catalogs_services_item_id',
        'catalogs_service_id',
        'service_id',
        'filial_id',

        'price',
        'name_alt',

        'discount_mode',
        'discount_percent',
        'discount_currency',

        'price_discount_id',
        'price_discount',
        'total_price_discount',

        'catalogs_item_discount_id',
        'catalogs_item_discount',
        'total_catalogs_item_discount',

        'estimate_discount_id',
        'estimate_discount',
        'total_estimate_discount',

        'total',

        'points',
        'currency_id',

        'archive',

        'status',
        'is_hit',
        'is_new',
        'is_priority',

        'is_show_price',

        'is_discount',
        'is_need_recalculate',

        'external',

        'display',
        'system',
        'moderation'
    ];


    // Каталог
    public function catalog()
    {
        return $this->belongsTo(CatalogsService::class, 'catalogs_service_id');
    }

    // Пункты каталога
    public function catalogs_item()
    {
        return $this->belongsTo(CatalogsServicesItem::class, 'catalogs_services_item_id');
    }

    // Филиал
    public function filial()
    {
        return $this->belongsTo(Department::class);
    }

    // Услуга
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function service_public()
    {
        return $this->belongsTo(Service::class, 'service_id')
            ->with('process')
            ->whereHas('process', function ($q) {
                $q->with([
                    'workflows'
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
        return $this->hasMany(PricesServicesHistory::class, 'prices_service_id');
    }

    // Актуальная цена
    public function actual_price()
    {
        return $this->hasOne(PricesServicesHistory::class, 'prices_service_id')
            ->whereNull('end_date');
    }

    // Предок
    public function ancestor()
    {
        return $this->belongsTo(PricesService::class);
    }

    // Последователь
    public function follower()
    {
        return $this->hasOne(PricesService::class, 'ancestor_id')
            ->where('archive', false);
    }

    // Общее отношение для товаров и услуг
    public function product()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Валюта
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_price_service', 'price_service_id', 'discount_id')
            ->withPivot([
                'sort'
            ])
            ->orderBy('pivot_sort');
    }

    public function discounts_actual()
    {
        return $this->belongsToMany(Discount::class, 'discount_price_service', 'price_service_id', 'discount_id')
            ->where('archive', false)
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>', now())
                    ->orWhereNull('ended_at');
            })
            ->withPivot([
                'sort'
            ])
            ->orderBy('pivot_sort');
    }

    public function discount_price()
    {
        return $this->belongsTo(Discount::class, 'price_discount_id');
    }

    public function discount_catalogs_item()
    {
        return $this->belongsTo(Discount::class, 'catalogs_item_discount_id');
    }

    public function discount_estimate()
    {
        return $this->belongsTo(Discount::class, 'estimate_discount_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'like_prices_service', 'prices_service_id', 'user_id');
    }

    /**
     * Вычисление итоговой стоимости услуги с учетом подключенных скидок.
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
            $priceServiceDiscounts = $this->discounts_actual;
            $totalWithoutDiscounts = $this->total;
            $resPrice = $this->getDynamicDiscounts($priceServiceDiscounts, $totalWithoutDiscounts);
            $total = $resPrice['total'];
        }

        // Вычисление всех скидок на разделе каталога, в котором находится прайс
        if (!$resPrice['break']) {
            $catalogsServicesItem = $this->catalogs_item;
            if ($catalogsServicesItem->is_discount == 1) {
                $catalogsServicesItemDiscounts = $catalogsServicesItem->discounts_actual;
                $resCatalogItem = $this->getDynamicDiscounts($catalogsServicesItemDiscounts, $total);
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

    // Фильтр
    public function scopeFilter($query)
    {
        if (request('catalogs_services_items')) {
            $query->whereIn('catalogs_services_item_id', request('catalogs_services_items'));
        }

        return $query;
    }
}
