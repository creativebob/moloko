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
    'department_name',
    'city_id',
  ];
  /**
  * Получаем филиал данного отдела.
  */
  public function filial()
  {
    return $this->belongsTo('App\Filial');
  }

}