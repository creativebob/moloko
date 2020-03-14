<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class PricesServicesHistory extends Model
{

    use Commonable;
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    protected $dates = [
        'deleted_at',
        'begin_date',
        'end_date'
    ];

    protected $fillable = [
        'price',
        'currency_id',
        'prices_service_id',

        'begin_date',
        'end_date',

        'display',
        'system',
        'moderation'
    ];

    // Прайс
    public function prices_service()
    {
        return $this->belongsTo(PricesService::class);
    }
}
