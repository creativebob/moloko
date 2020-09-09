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

class PricesService extends Model
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

    protected $fillable = [
        'catalogs_services_item_id',
        'catalogs_service_id',
        'service_id',
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

        'is_show_price',
        'is_need_recalculate',

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

        if (request('length')) {
            $weight = request('length');
            $query->whereHas('service_public', function($q) use ($weight) {
                $q->whereHas('process', function($q) use ($weight) {
                    $q->where('length', '>=', $weight['min'] / 1000)
                        ->where('length', '<=', $weight['max'] / 1000);
                });
            });
        }

        if (request('catalogs_services_item')) {
            $catalogs_services_item = request('catalogs_services_item');
            $query->where('catalogs_services_item_id', $catalogs_services_item);
        }

//        if (request('raws_articles_groups')) {
//            $raws_articles_groups = request('raws_articles_groups');
////		    dd($raws_articles_groups);
//
//            $query->whereHas('goods_public', function($q) use ($raws_articles_groups) {
//                $q->whereHas('article', function($q) use ($raws_articles_groups) {
//                    foreach($raws_articles_groups as $item){
//                        $q->whereHas('attachments',function($q) use ($item) {
//                            $q->whereHas('article', function ($q) use ($item) {
//                                $q->where('articles_group_id', $item);
//                            });
//                        });
//                    }
//                });
//            });
//        }

        return $query;
    }
}
