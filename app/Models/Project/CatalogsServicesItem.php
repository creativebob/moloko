<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;

class CatalogsServicesItem extends Model
{
    use Publicable;
	use Cachable;

	// Каталог
	public function catalog()
	{
		return $this->belongsTo(CatalogsService::class, 'catalogs_service_id')
            ->display();
	}

    // Родитель
    public function parent()
    {
        return $this->belongsTo(CatalogsServicesItem::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(CatalogsServicesItem::class, 'parent_id');
    }

    // Главный
    public function category()
    {
        return $this->belongsTo(CatalogsServicesItem::class);
    }

    // Прайсы
    public function prices()
    {
        return $this->hasMany(PricesService::class)
            ->display()
            ->archive()
            ->has('service');
    }


    public function childs_prices()
    {
        return $this->hasManyThrough(PricesService::class, CatalogsServicesItem::class, 'parent_id', 'catalogs_services_item_id')
            ->display(true, 'prices_service')
            ->archive(false, 'prices_service')
            ->has('service');
    }

    // Фильтры
    public function filters()
    {
        return $this->belongsToMany('App\Metric', 'filters_services', 'catalogs_services_item_id', 'metric_id');
    }

    public function directive_category()
    {
        return $this->belongsTo('App\UnitsCategory');
    }

    public function getNameWithParentAttribute()
    {
        if($this->parent_id != null){
            return $this->parent->name . ' / ' . $this->name;
        } else {
            return $this->name;
        }
    }

}
