<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class EstimatesServicesItem extends Model
{
    // Включаем кеш
    use Cachable;

    // use SoftDeletes;

    use Commonable;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'estimate_id',

        'price_id',
        'currency_id',

        'service_id',

        'company_id',
        'author_id',

        'count',
        'price',

        'comment',

        'cost',
        'margin_percent',
        'margin_currency',

        'amount',
        'total',

        'display',
        'system',
        'moderation'
    ];

    // Смета
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function document()
    {
        return $this->belongsTo(Estimate::class, 'estimate_id');
    }

    // Прайс
    public function price()
    {
        return $this->belongsTo(PricesService::class);
    }

    public function price_service()
    {
        return $this->belongsTo(PricesService::class, 'price_id');
    }

    // Услуга
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function product()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

}
