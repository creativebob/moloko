<?php

namespace App;

use App\Models\System\Traits\Commonable;
use App\Models\System\Traits\Processable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Service extends Model
{

	// Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    use Commonable;
    use Processable;

    protected $fillable = [
        'category_id',
        'process_id',

        'display',
        'system',
        'moderation'
    ];

    // Процесс
    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    // Категория
    public function category()
    {
        return $this->belongsTo(ServicesCategory::class);
    }

    // Пункты каталога
    public function catalogs_items()
    {
        return $this->belongsToMany(CatalogsServicesItem::class, 'prices_services', 'service_id', 'catalogs_services_item_id');
    }

    // Пункты каталога
    public function prices()
    {
        return $this->hasMany(PricesService::class)
        ->where('archive', false);
    }

}
