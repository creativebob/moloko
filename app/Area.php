<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
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
