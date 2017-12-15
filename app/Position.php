<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'position_name',
    'page_id',
    'direct_status',
    'company_id',
  ];
  /**
   * Получаем районы и города области.
   */
  public function page()
  {
    return $this->belongsTo('App\Page');
  }
  /**
   * Получаем сотрудников должности.
   */
  public function employees()
  {
    return $this->hasMany('App\Employee');
  }
}
