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

class CatalogsItem extends Model
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
		'parent_id',
		'category_id',
		'catalog_id',
	];

	// Каталог
	public function catalog()
	{
		return $this->belongsTo(Catalog::class);
	}

    // Вложенные
	public function childs()
	{
		return $this->hasMany(CatalogsItem::class, 'parent_id');
	}

	// Автор
	public function author()
	{
		return $this->belongsTo(User::class);
	}

	// Товары каталога
    public function goods()
    {
        return $this->belongsToMany(Goods::class, 'catalogs_items_goods', 'catalogs_item_id', 'goods_id');
    }
}
