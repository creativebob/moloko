<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
  use SoftDeletes;
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
