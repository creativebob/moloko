<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Заготовки
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Фильтры
use App\Scopes\Filters\CityFilter;
use App\Scopes\Filters\BooklistFilter;

class Product extends Model
{
	use Notifiable;
	use SoftDeletes;

  // Подключаем Scopes для главного запроса
	use CompaniesLimitTraitScopes;
	use AuthorsTraitScopes;
	use SystemItemTraitScopes;
	use FilialsTraitScopes;
	use TemplateTraitScopes;
	use ModeratorLimitTraitScopes;
	use BooklistFilter;

	protected $dates = ['deleted_at'];

	protected $fillable = [
		'name',
		'article',
		'cost',
		'description',

	];

	// Получаем компанию
	public function company()
	{
		return $this->belongsTo('App\Company');
	}

  // Получаем категорию
	public function products_category()
	{
		return $this->belongsTo('App\ProductsCategory');
	}

	// Получаем автора
	public function author()
	{
		return $this->belongsTo('App\User', 'author_id');
	}

	// Получаем страну
  public function country()
  {
    return $this->belongsTo('App\Country');
  }

  // Получаем еденицу измерения
  public function unit()
  {
    return $this->belongsTo('App\Unit');
  }
}
