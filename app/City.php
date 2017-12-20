<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
	use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
	protected $table = 'cities';
  protected $fillable = [
      'city_name',
      'region_id',
      'area_id',
      'city_code',
      'city_vk_external_id',
  ];
  /**
  * Получаем район данного города.
  */
  public function area()
  {
    return $this->belongsTo('App\Area');
  }
  // *
  // * Получаем область данного города.
  public function region()
  {
    return $this->belongsTo('App\Region');
  }
  /**
  * Получаем филиалы и отделы города.
  */
  public function departments()
  {
    return $this->hasMany('App\Department');
  }
}
