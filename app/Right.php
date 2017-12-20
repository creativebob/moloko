<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
        protected $fillable = [
       	'right_name', 
		'right_action', 
		'entity_type', 
		'entity_id', 
    ];
    /**
	   * Роли, принадлежащие пользователю.
	   */
	  public function roles()
	  {
	    return $this->belongsToMany('App\Role');
	  }
	  /**
	   *  Связь с таблицей прав.
	   */
	  public function access_groups()
	  {
	    return $this->belongsToMany('App\Access_group', 'accesses', 'access_group_id', 'id');
	  }
}
