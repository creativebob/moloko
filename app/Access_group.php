<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Access_group extends Model
{
	use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'id', 
        'access_group_name', 
        'category_right_id', 
    ];
    /**
	  * Получаем пользователей группы.
	  */
	  public function users()
	  {
	    return $this->hasMany('App\User');
	  }

	  /**
	   *  Связь с таблицей прав.
	   */
	  public function rights()
	  {
	    return $this->belongsToMany('App\Right', 'accesses', 'right_action', 'right_action');
	  }

}
