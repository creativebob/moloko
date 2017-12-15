<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
	use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'company_id',
    'city_id',
    'department_name',
    'department_address',
    'department_phone',
    'department_parent_id',
    'filial_status',
    'filial_id',
  ];
  /**
   * Получаем должности.
   */
  public function employees()
  {
    return $this->hasMany('App\Employee');
  }
}