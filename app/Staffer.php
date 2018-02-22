<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\ModerationScope;
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class Staffer extends Model
{

  use SoftDeletes;
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
  protected $table = 'staff';
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'user_id',
    'position_id',
    'department_id',
    'filial_id'
  ];

  /**
  * Получаем отдел данной должности.
  */
  public function department()
  {
    return $this->belongsTo('App\Department');
  }
  /**
  * Получаем Филиал данной должности.
  */
  public function filial()
  {
    return $this->belongsTo('App\Department', 'filial_id');
  }

  public function position()
  {
    return $this->belongsTo('App\Position');
  }

  public function user()
  {
    return $this->belongsTo('App\User');
  }
  /**
  * Получаем сотрудников.
  */
  public function employees()
  {
    return $this->hasMany('App\Employee');
  }
  /**
  * Получаем компанию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }
  /**
  * Получаем автора
  */
   public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }

}
