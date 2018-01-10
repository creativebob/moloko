<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Navigation extends Model
{
    use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
    protected $dates = ['deleted_at'];
    protected $fillable = [
    	'navigation_name',
      'site_id',
      'company_id',

    ];

    /**
  * Получаем пункты навигации.
  */
  public function menus()
  {
    return $this->hasMany('App\Menu');
  }
     /**
  * Получаем сайт навигации.
  */
  public function site()
  {
    return $this->belongsTo('App\Site');
  }
}
