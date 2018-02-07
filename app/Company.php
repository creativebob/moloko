<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Scopes\ModerationScope;

use App\Scopes\Traits\CompaniesFilterTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\ModerationTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Company extends Model
{

    use Notifiable;
    use SoftDeletes;

    // Подключаем Scopes для главного запроса
    use CompaniesFilterTraitScopes;
    use AuthorsTraitScopes;
    use SystemitemTraitScopes;
    use FilialsTraitScopes;
    use ModeratorFilterTraitScopes;

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
  /**
  * Получаем штат компании.
  */
  public function staff()
  {
    return $this->hasMany('App\Staffer');
  } 

}
