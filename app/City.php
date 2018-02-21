<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\ModerationScope;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class City extends Model
{

	use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
	protected $table = 'cities';
  protected $fillable = [
      'city_name',
      'region_id',
      'area_id',
      'city_code',
      'city_vk_external_id',
  ];
  /**
  * Получаем район данного города.
  */
  public function area()
  {
    return $this->belongsTo('App\Area');
  }
  // *
  // * Получаем область данного города.
  public function region()
  {
    return $this->belongsTo('App\Region');
  }
  /**
  * Получаем филиалы и отделы города.
  */
  public function departments()
  {
    return $this->hasMany('App\Department');
  }
    /**
  * Получаем пользователей.
  */
  public function users()
  {
    return $this->hasMany('App\User');
  }
}
