<?php

namespace App\Models\System\Parser;

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
use App\Scopes\Traits\SuppliersTraitScopes;
use App\Scopes\Traits\ManufacturersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Phone extends Model
{
    // Включаем кеш
	use Cachable;

	use Notifiable;

    // Включаем Scopes
	use CompaniesLimitTraitScopes;
	use AuthorsTraitScopes;
	use SystemItemTraitScopes;
	use FilialsTraitScopes;
	use TemplateTraitScopes;
	use ModeratorLimitTraitScopes;

    // Фильтры
	use Filter;
	use BooklistFilter;
    // use DateIntervalFilter;

	protected $fillable = [
		'phone',
		'crop',

        'display',
        'system',
        'moderation',

        'created_at',
        'company_id',
        'author_id'
	];

    // Получаем компанию
	public function company()
	{
		return $this->belongsTo('App\Company');
	}

    // Получаем автора
	public function author()
	{
		return $this->belongsTo('App\User', 'author_id');
	}

	// Компании
	public function companies()
	{
		return $this->morphedByMany('App\Company', 'phone_entity');
	}

	// Пользователь владелец номера
	public function user_owner()
	{
		return $this->morphedByMany('App\User', 'phone_entity', 'phone_entities');
	}

}
