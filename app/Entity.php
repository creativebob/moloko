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
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Entity extends Model
{
  use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use ModeratorFilterTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'entity_name',
  	'entity_alias',
  ];


    /**
  * Получаем действия над сущностью
  */
  public function actions()
  {
    return $this->belongsToMany('App\Action', 'action_entity', 'entity_id', 'action_id');
  }


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function actionentities()
  {
    return $this->hasMany('App\Actionentity');
  }


}
