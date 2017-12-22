<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RightRole extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $table = 'right_role';
  protected $dates = ['deleted_at'];
  protected $fillable = [
    	'id', 
        'right_id', 
        'role_id', 
    ];
}
