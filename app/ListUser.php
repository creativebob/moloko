<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListUser extends Model
{
	protected $table = 'list_user';


    /**
  * Получаем запись (ID списка).
  */
  public function booklist()
  {
    return $this->belongsTo('App\Booklist');
  }


    /**
  * Получаем запись (ID пользователя).
  */
  public function user()
  {
    return $this->belongsTo('App\User');
  }

}
