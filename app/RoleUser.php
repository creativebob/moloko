<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class RoleUser extends Model
{
  // Подключаем Scopes для главного запроса
  use CompaniesLimitTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $table = 'role_user';
  protected $fillable = [
    	'id', 
      'role_id', 
      'user_id', 
      'department_id',
      'position_id',
      'author_id',
    ];

  /*
    * Получаем категорию.
  */
  public function department()
  {
    return $this->belongsTo('App\Department');
  }

  public function position()
  {
    return $this->belongsTo('App\Position');
  }

  public function role()
  {
    return $this->belongsTo('App\Role');
  }
}
