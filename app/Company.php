<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

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

  protected $dates = ['deleted_at'];
  protected $fillable = [

      'company_name', 
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
    public function scopeCompanyFilter($query, $request)
    {

      //Фильтруем по городу
      if($request->city_id){
        $query = $query->where('city_id', $request->city_id);
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

}
