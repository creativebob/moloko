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

    const ALIAS = 'services';
    const DEPENDENCE = false;

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

    /**
     * Фильтр
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query)
    {
        $filters = $this->getFilters(self::ALIAS);

        if (isset($filters['services_categories'])) {
            $query->whereIn('category_id', request('services_categories'));
        }

        if (isset($filters['authors'])) {
            $query->whereIn('author_id', request('authors'));
        }
    }

}
