<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $fillable = [
    	'id', 
        'role_id', 
        'user_id', 
    ];
}
