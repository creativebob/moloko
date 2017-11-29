<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'city_name',
        'city_region',
        'city_area',
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
}
