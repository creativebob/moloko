<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class OrderComposition extends Model
{
    // Включаем кеш
	use Cachable;

	// use SoftDeletes;

    // Включаем Scopes
	use CompaniesLimitTraitScopes;
	use AuthorsTraitScopes;
	use SystemItemTraitScopes;
	use FilialsTraitScopes;
	use TemplateTraitScopes;
	use ModeratorLimitTraitScopes;

    // Фильтры
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

	protected $dates = ['deleted_at'];
	protected $fillable = [
		'order_compositions_id',
        'order_compositions_type',
        'order_id',
        'count',
        'company_id',
        'author_id'
	];

	public function order()
	{
		return $this->belongsTo('App\Order');
	}

	 // Получаем комментарии
    public function notes()
    {
        return $this->morphMany('App\Note', 'notes');
    }

	public function product()
	{
		return $this->morphTo();
	}
}
