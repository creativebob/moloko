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
		'company_id',
		'name',
		'alias',
        'slug',
		'parent_id',
		'category_id',
		'catalogs_service_id',
        'author_id',
        'display',
	];

	// Каталог
	public function catalog()
	{
		return $this->belongsTo(CatalogsService::class);
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

	// Автор
	public function author()
	{
		return $this->belongsTo(User::class);
	}

	// Услуги каталога
    public function services()
    {
        return $this->belongsToMany(Service::class, 'price_service', 'catalogs_services_item_id', 'service_id')
        ->withPivot([
            'price'
        ]);
    }
}
