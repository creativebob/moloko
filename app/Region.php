<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
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
