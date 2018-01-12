<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
    // protected $table = 'menu';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    	'menu_name',
      'menu_parent_id',
      'page_id',
      'table_id',

    ];

      /**
  * Получаем навигацию меню.
  */
  public function navigation()
  {
    return $this->belongsTo('App\Navigation');
  }
    /**
  * Получаем страницу меню.
  */
  public function page()
  {
    return $this->belongsTo('App\Page');
  }
}
