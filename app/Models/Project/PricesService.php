<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class PricesService extends Model
{
    // Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    // Каталог
    public function catalog()
    {
        return $this->belongsTo('App\CatalogsService', 'catalogs_service_id');
    }

    // Пункты каталога
    public function catalogs_item()
    {
        return $this->belongsTo('App\CatalogsServicesItem', 'catalogs_services_item_id');
    }

    public function catalogs_item_public()
    {
        return $this->belongsTo('App\CatalogsServicesItem', 'catalogs_services_item_id')
            ->where('display', true);
    }

    // Филиал
    public function filial()
    {
        return $this->belongsTo('App\Department');
    }

    // Товар
    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function service_public()
    {
        return $this->belongsTo('App\Service', 'service_id')
            ->with('process')
//            ->whereHas('process', function ($q) {
//                $q->with([
//                    'raws'
//                ])
//                    ->where([
//                        'draft' => false
//                    ]);
//            })
            ->where([
                'display' => true,
                'archive' => false,
            ]);
    }

    // История
    public function history()
    {
        return $this->hasMany('App\PricesServicesHistory', 'prices_service_id');
    }

    // Актуальная цена
    public function actual_price()
    {
        return $this->hasOne('App\PricesServicesHistory', 'prices_service_id')
            ->whereNull('end_date');
    }

    // Предок
    public function ancestor()
    {
        return $this->belongsTo('App\PricesService');
    }

    // Последователь
    public function follower()
    {
        return $this->hasOne('App\PricesService', 'ancestor_id')
            ->where('archive', false);
    }

    // Общее отношение для товаров и услуг
    public function product()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }

    // Валюта
    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

}
