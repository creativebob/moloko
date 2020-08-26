<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
// use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class CatalogsServicesItem extends Model
{

	// Включаем кеш
	use Cachable;

	use SoftDeletes;

    // Включаем Scopes
	use CompaniesLimitTraitScopes;
	use AuthorsTraitScopes;
	use SystemItemTraitScopes;
	// use FilialsTraitScopes;
	use TemplateTraitScopes;
	use ModeratorLimitTraitScopes;

    // Фильтры
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

	protected $dates = ['deleted_at'];

	protected $fillable = [
        'name',
        'description',
        'header',
        'title',
        'seo_description',
        'parent_id',
        'photo_id',
		'catalogs_service_id',
        'color',

        'display_mode_id',
        'directive_category_id',

        'is_controllable_mode',
        'is_show_subcategory',
        'is_hide_submenu',

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

    public function getNameWithParentAttribute()
    {
        if($this->parent_id != null){
            return $this->parent->name . ' / ' . $this->name;
        } else {
            return $this->name;
        }
    }

}
