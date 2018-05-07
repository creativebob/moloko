<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class NavigationsCategory extends Model
{
	use SoftDeletes;
  // Подключаем Scopes для главного запроса
	use CompaniesLimitTraitScopes;
	use AuthorsTraitScopes;
	use SystemItemTraitScopes;
	use FilialsTraitScopes;
	use TemplateTraitScopes;
	use ModeratorLimitTraitScopes;

	 /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
	 protected $dates = ['deleted_at'];
	 protected $fillable = [
	 	'name',
	 	'parent_id',
	 	'category_status',
	 ];

    /**
  * Получаем компании.
  */
   public function company()
    {
    	return $this->belongsTo('App\Company');
    }

    public function navigations()
    {
    	return $this->hasMany('App\Navigation');
    }
}
