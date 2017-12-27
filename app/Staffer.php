<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staffer extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $table = 'staff';
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'user_id',
    'position_id',
    'department_id',
  ];


 
  /**
  * Получаем отдел данной должности.
  */
  public function department()
  {
    return $this->belongsTo('App\Department');
  }
  /**
  * Получаем должность.
  */
  public function position()
  {
    return $this->belongsTo('App\Position');
  }
  /**
  * Получаем юзера.
  */
  public function user()
  {
    return $this->belongsTo('App\User');
  }
  /**
  * Получаем сотрудников.
  */
  public function employees()
  {
    return $this->hasMany('App\Employee');
  }

}