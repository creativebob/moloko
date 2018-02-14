<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


// Фильтры
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

  // Связываем компанию с информацией о ее директоре
  public function director()
  {
    return $this->BelongsTo('App\User', 'director_user_id');
  }

  public function departments()
  {
    return $this->hasMany('App\Department');
  }

  public function sites()
  {
    return $this->hasMany('App\Site');
  }

  public function users()
  {
    return $this->hasMany('App\User');
  }

  public function roles()
  {
    return $this->hasMany('App\Role');
  } 

  public function staff()
  {
    return $this->hasMany('App\Staffer');
  } 

  public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }
  /**
  * Получаем город.
  */
  public function city()
  {
    return $this->belongsTo('App\City');
  }

}
