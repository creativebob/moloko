<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
  use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */

	public function user_info()
	{
		return $this->BelongsTo('App\User', 'director_user_id');
	}

    protected $dates = ['deleted_at'];
    protected $fillable = [

        'company_name', 
        'company_phone', 
        'company_extra_phone', 
        'city_id', 
        'company_address', 
        'company_inn', 
        'kpp', 
        'account_settlement', 
        'account_correspondent', 
        'bank', 
        'director_user_id', 
        'admin_user_id'
    ];
  /**
  * Получаем отделы компании.
  */
  public function departments()
  {
    return $this->hasMany('App\Department');
  }
  /**
  * Получаем сайты компании.
  */
  public function sites()
  {
    return $this->hasMany('App\Site');
  }
  /**
  * Получаем пользователей компании.
  */
  public function users()
  {
    return $this->hasMany('App\User');
  }
  /**
  * Получаем роли компании.
  */
  public function roles()
  {
    return $this->hasMany('App\Role');
  } 
}
