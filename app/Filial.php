<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filial extends Model
{
   use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'filial_name',
    'filial_address',
    'filial_phone',
  ];
  /**
   * Получаем отделы.
   */
  public function departmens()
  {
    return $this->hasMany('App\Department');
  }

}
