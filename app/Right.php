<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Right extends Model
{
		use SoftDeletes;
		
    // БЛОК ОПИСАНИЯ ФИЛЬТРОВ:

    // Фильтрация для показа системных записей
    public function scopeSystemItem($query, $system_item)
    {
        if(isset($system_item)){
          return $query->where('system_item', '=', $system_item);
        } else {return $query;};
    }

  		protected $dates = ['deleted_at'];
      protected $fillable = [
      'right_name', 
		  'right_action', 
		  'category_right_id', 
    ];
      /**
  * Получаем права.
  */
  public function roles()
  {
    return $this->belongsToMany('App\Role')->withPivot('category_right_id', 'directive', 'object_entity', 'right_name');
  }

    /**
  * Получаем категорию права.
  */
  public function сategory_right()
  {
    return $this->belongsTo('App\Сategory_right');
  }

  public function actionentity()
  {
    return $this->hasOne('App\ActionEntity', 'id', 'object_entity');
  }

}
