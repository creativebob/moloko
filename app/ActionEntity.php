<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionEntity extends Model
{

  protected $table = 'action_entity';


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function right()
  {
    return $this->hasOne('App\Right', 'action_entity', 'id', 'object_entity');
  }


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function entity()
  {
    return $this->belongsTo('App\Entity');
  }


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function action()
  {
    return $this->belongsTo('App\Action');
  }

}
