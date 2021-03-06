<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class CatalogsServicesItem extends BaseModel
{
	use Cachable,
        SoftDeletes;

    const ALIAS = 'catalogs_services_items';
    const DEPENDENCE = false;

	protected $dates = [
	    'deleted_at'
    ];

	protected $fillable = [
        'name',
        'description',
        'parent_id',
        'photo_id',
		'catalogs_service_id',
        'color',

        'display_mode_id',
//        'directive_category_id',

        'is_controllable_mode',
        'is_show_subcategory',
        'is_hide_submenu',

        'is_discount',

        'video_url',
        'video',

        'seo_id',

        'display',
        'system',
        'moderation'
	];

	// Каталог
	public function catalog()
	{
		return $this->belongsTo(CatalogsService::class, 'catalogs_service_id');
	}

    public function catalog_public()
    {
        return $this->belongsTo(CatalogsService::class, 'catalogs_service_id')
            ->where('display', true);
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

    public function children()
    {
        return $this->hasMany(CatalogsServicesItem::class, 'parent_id')
            ->with('children');
    }

    // Главный
    public function category()
    {
        return $this->belongsTo(CatalogsServicesItem::class);
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

	// Автор
	public function author()
	{
		return $this->belongsTo(User::class);
	}

	// Прайс
	public function prices_services()
	{
		return $this->hasMany(PricesService::class);
	}

    public function prices_services_actual()
    {
        return $this->hasMany(PricesService::class)
            ->where('archive', false);
    }

    public function prices()
    {
        return $this->hasMany(PricesService::class);
    }

    public function prices_public()
    {
        return $this->hasMany(PricesService::class)
            ->has('service_public')
            ->where([
                'display' => true,
                'archive' => false
            ]);
    }

	// Услуги каталога
    public function services()
    {
        return $this->belongsToMany(Service::class, 'prices_services', 'catalogs_services_item_id', 'service_id')
        ->withPivot([
            'price'
        ]);
    }

    // Фильтры
    public function filters()
    {
        return $this->belongsToMany(Metric::class, 'filters_services', 'catalogs_services_item_id', 'metric_id');
    }

    public function display_mode()
    {
        return $this->belongsTo(DisplayMode::class);
    }

    public function directive_category()
    {
        return $this->belongsTo(UnitsCategory::class);
    }

    public function seo()
    {
        return $this->belongsTo(Seo::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_catalogs_services_item', 'catalogs_services_item_id', 'discount_id')
            ->withPivot([
                'sort'
            ])
            ->orderBy('pivot_sort');
    }

    public function discounts_actual()
    {
        return $this->belongsToMany(Discount::class, 'discount_catalogs_services_item', 'catalogs_services_item_id', 'discount_id')
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

    public function getNameWithParentAttribute()
    {
        if($this->parent_id != null){
            return $this->parent->name . ' / ' . $this->name;
        } else {
            return $this->name;
        }
    }

}
