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
}
