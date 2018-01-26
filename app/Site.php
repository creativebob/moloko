<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'site_name',
    'site_domen',
    'company_id',
  ];
  /**
   * Получаем страницы.
   */
  public function pages()
  {
    return $this->hasMany('App\Page');
  }
    /**
  * Получаем пункты меню.
  */
  public function menus()
  {
    return $this->belongsToMany('App\Menu');
  }
  /**
   * Получаем навигации.
   */
  public function navigations()
  {
    return $this->hasMany('App\Navigation');
  }
  /**
  * Получаем компанию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }
  /**
  * Получаем автора
  */
   public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }
  
}
