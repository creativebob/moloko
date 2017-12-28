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
  protected $table = 'role_user';
  protected $fillable = [
    	'id', 
      'role_id', 
      'user_id', 
      'department_id',
      'position_id',
      'author_id',
    ];
  /*
    * Получаем категорию.
  */
  public function department()
  {
    return $this->belongsTo('App\Department');
  }
  public function position()
  {
    return $this->belongsTo('App\Position');
  }
  public function role()
  {
    return $this->belongsTo('App\Role');
  }
}
