<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


// Заготовки
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Фильтры
use App\Scopes\Filters\CityFilter;
use App\Scopes\Filters\SectorFilter;
use App\Scopes\Filters\BooklistFilter;


class Company extends Model
{

  use Notifiable;
  use SoftDeletes;

  // Подключаем Scopes для главного запроса
  use CompaniesLimitTraitScopes;
  use AuthorsTraitScopes;
  use SystemItemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;

  // Подключаем фильтры
  use CityFilter;
  use SectorFilter;
  use BooklistFilter;

  protected $dates = ['deleted_at'];
  protected $fillable = [

      'company_name', 
      'company_alias', 
      'phone', 
      'extra_phone', 
      'email', 
      'city_id', 
      'address', 
      'inn', 
      'kpp', 
      'account_settlement', 
      'account_correspondent', 
      'bank', 
      'director_user_id', 
      'admin_user_id'
  ];

    // Фильтрация по городу
    public function scopeAuthorFilter($query, $request)
    {

        //Фильтруем по списку городов
        if($request->author_id){
          $query = $query->whereIn('author_id', $request->author_id);
        };

      return $query;
    }


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

  public function city()
  {
    return $this->belongsTo('App\City');
  }

  public function positions()
  {
    return $this->hasMany('App\Position');
  }
  // Получаем сектор компании
  public function sector()
  {
    return $this->belongsTo('App\Sector');
  }

  // Получаем 
  public function worktime()
  {
    return $this->belongsTo('App\Worktime');
  }


}
