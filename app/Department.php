<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
	use SoftDeletes;



    // Фильтрация для показа системных записей
    public function scopeSystemItem($query, $system_item)
    {
        if(isset($system_item)){
          return $query->orWhere('system_item', '1');
        } else {return $query;};
    }

        // Фильтрация для показа системных записей
    public function scopeOtherItem($query, $other_item)
    {
        if(isset($other_item)){

            if(isset($other_item['all'])){
                return $query;
            } else {
                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->WhereIn('author_id', $other_item)->orWhere('author_id', $other_item['user_id'])->orWhere('id', $other_item['user_id']);
            }
        }
    }

  
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'company_id',
    'city_id',
    'department_name',
    'department_address',
    'department_phone',
    'department_parent_id',
    'filial_status',
    'filial_id',
  ];
  /**
  * Получаем компанию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }
  /**
   * Получаем должности.
   */
  public function employees()
  {
    return $this->hasMany('App\Employee');
  }
  /**
  * Получаем город.
  */
  public function city()
  {
    return $this->belongsTo('App\City');
  }
  /**
  * Получаем роли филиала.
  */
  public function roles()
  {
    return $this->hasMany('App\Role');
  } 

}