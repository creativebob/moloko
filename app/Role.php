<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
 	use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
    	'id', 
        'role_name', 
        'category_right_id', 
    ];

    /**
  * Получаем пользователей.
  */
  public function users()
  {
    return $this->belongsToMany('App\User');
  }
    /**
  * Получаем права.
  */
  public function rights()
  {
    return $this->belongsToMany('App\Right');
  }

}
