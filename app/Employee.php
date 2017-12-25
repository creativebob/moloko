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
  	'vacancy_id',
    'user_id',
    'date_employment',
    'date_dismissal',
  ];
  /**
  * Получаем вакансию для сотрудников.
  */
  public function staffer()
  {
    return $this->belongsTo('App\Staffer');
  }
  /**
  * Получаем сотрудника.
  */
  public function user()
  {
    return $this->belongsTo('App\User');
  }
}
