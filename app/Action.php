<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{

 	use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'action_name', 
        'action_method', 
    ];

    /**
  * Получаем действия над сущностью
  */
  public function entities()
  {
    return $this->belongsToMany('App\Entity', 'action_entity', 'action_id', 'entity_id');
  }


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function actionentities()
  {
    return $this->hasMany('App\ActionEntity');
  }


}

