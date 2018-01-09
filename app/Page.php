<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
	public function setMydateAttribute($value){
	    $date_parts = explode('.', $value);
	    $this->attributes['mydate'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
	}
  protected $fillable = [
    'page_name', 
  	'site_id ', 
  	'page_title', 
  	'page_description', 
  	'page_alias', 
  ];

  // БЛОК ОПИСАНИЯ ФИЛЬТРОВ:
  // Фильтрация по статусу пользователя: клиент или сотрудник
  public function scopeSiteId($query, $site_id)
  {
    if(isset($site_id)){
      return $query->where('site_id', $site_id);
    }
  }
  // КОНЕЦ БЛОКА ОПИСАНИЯ ФИЛЬТРОВ

  /**
  * Получаем сайт страницы.
  */
  public function site()
  {
    return $this->belongsTo('App\Site');
  }
  /**
  * Получаем должность страницы.
  */
  public function position()
  {
    return $this->hasOne('App\Position');
  }
  /**
  * Получаем пункты меню.
  */
  public function menu()
  {
    return $this->hasOne('App\Menu');
  }
}
