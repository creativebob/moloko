<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'user_id',
    'position_id',
    'department_id',
    'car_id',
    'date_employment',
    'date_dismissal',
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

}
