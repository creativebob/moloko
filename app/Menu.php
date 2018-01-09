<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    // protected $table = 'menu';
    protected $fillable = [
    	'menu_name',
        'menu_parent_id',
        'page_id',
        'table_id',

    ];

    /**
  * Получаем страницу меню.
  */
  public function page()
  {
    return $this->belongsTo('App\Page');
  }
}
