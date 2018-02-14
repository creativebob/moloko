<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\ModerationScope;
use App\Scopes\Traits\CompaniesFilterTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Access_group extends Model
{
	use SoftDeletes;
	// Подключаем Scopes для главного запроса
  	use CompaniesFilterTraitScopes;
	use AuthorsTraitScopes;
	use SystemitemTraitScopes;
	use FilialsTraitScopes;
	use TemplateTraitScopes;
	use ModeratorFilterTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'id', 
        'access_group_name', 
        'category_right_id', 
    ];
    /**
	  * Получаем пользователей группы.
	  */
	  public function users()
	  {
	    return $this->hasMany('App\User');
	  }

	  /**
	   *  Связь с таблицей прав.
	   */
	  public function rights()
	  {
	    return $this->belongsToMany('App\Right', 'accesses', 'right_action', 'right_action');
	  }

}
