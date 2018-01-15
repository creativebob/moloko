<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BooklistUser extends Model
{
	protected $table = 'booklist_user';


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
