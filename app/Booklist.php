<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booklist extends Model
{
 	use SoftDeletes;
		
  	protected $dates = ['deleted_at'];
    protected $fillable = [
	    'list_name', 
	    'list_description', 
    ];

  public function list_items()
  {
    return $this->hasMany('App\List_item');
  }

    /**
  * Получаем пользователей
  */
  public function users()
  {
    return $this->belongsToMany('App\User');
  }

}
