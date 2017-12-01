<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
	use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'area_name',
    'region_id',
  ];

  /**
  * Получаем область данного района.
  */
  public function region()
  {
    return $this->belongsTo('App\Region');
  }

  /**
  * Получаем города района.
  */
  public function cities()
  {
    return $this->hasMany('App\City');
  }
}
