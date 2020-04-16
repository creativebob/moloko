<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;

class PricesService extends Model
{
    use Publicable;
    use Cachable;

    protected $with = [
        'service',
        'catalogs_item',
        'currency'
    ];

    // Каталог
    public function catalog()
    {
        return $this->belongsTo(CatalogsService::class, 'catalogs_service_id')
            ->display();
    }

    // Раздел
    public function catalogs_item()
    {
        return $this->belongsTo(CatalogsServicesItem::class, 'catalogs_services_item_id')
            ->display();
    }

    // Филиал
    public function filial()
    {
        return $this->belongsTo('App\Department');
    }

    // Услуга
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id')
            ->display()
            ->archive()
            ->has('process');
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
