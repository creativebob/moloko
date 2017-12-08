<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'region_name',
    'region_code',
    'region_vk_external_id',
  ];
  /**
   * Получаем районы и города области.
   */
  public function areas()
  {
    return $this->hasMany('App\Area');
  }
  public function cities()
  {
    return $this->hasMany('App\City');
  }
}
