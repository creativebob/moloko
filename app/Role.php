<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Role extends Model
{

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
  	'id', 
    'role_name', 
    'role_description', 
    'category_right_id', 
  ];

  public function users()   
  {
    return $this->belongsToMany('App\User')->withPivot('department_id');
  }
  /**
  * Получаем права.
  */
  public function rights()
  {
    return $this->belongsToMany('App\Right');
  }
    /**
  * Получаем должности.
  */

    /**
  * Получаем категорию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }
      /**
  * Получаем категорию.
  */
  public function department()
  {
    return $this->belongsTo('App\Department');
  }

  public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }


    public function positions()
  {
    return $this->belongsToMany('App\Position', 'role_user', 'role_id', 'position_id');
  }

  public function departments()
  {
    return $this->belongsToMany('App\Department', 'role_user', 'role_id', 'department_id');
  }
  
}
