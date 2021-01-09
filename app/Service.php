<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Processable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends BaseModel
{
	// Включаем кеш
//    use Cachable;
    use SoftDeletes;
    use Processable;

    protected $fillable = [
        'category_id',
        'process_id',

        'archive',
        'serial',

        'display',
        'system',
        'moderation'
    ];

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
