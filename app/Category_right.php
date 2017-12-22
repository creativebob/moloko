<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category_right extends Model
{
    /**
  * Получаем права категории.
  */
  public function rights()
  {
    return $this->hasMany('App\Rights');
  }


    /**
  * Получаем роли.
  */
  public function roles()
  {
    return $this->hasMany('App\Role');
  }

  
}
