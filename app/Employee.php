<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Заготовки
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;


// Фильтры
use App\Scopes\Filters\PositionFilter;
use App\Scopes\Filters\DateIntervalFilter;
use App\Scopes\Filters\DepartmentFilter;

class Employee extends Model
{

  use SoftDeletes;

  // Подключаем Scopes для главного запроса
  use CompaniesLimitTraitScopes;
  use AuthorsTraitScopes;
  use SystemItemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;

  // Фильтры
  use PositionFilter;
  use DateIntervalFilter;
  use DepartmentFilter;
  
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'user_id',
    'employment_date',
    'dismissal_date',
  ];
  
   public function setEmploymentDateAttribute($value) {
    if($value == Null){
        return $value;
    } else {
        $date_parts = explode('.', $value);
        $this->attributes['employment_date'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
    };
  }

  public function getEmploymentDateAttribute($value) {
    if($value == Null){
        return $value;
    } else {
      $date_parts = explode('-', $value);
      $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
      return $value;
    };
  }

  public function setDismissalDateAttribute($value) {
    if($value == Null){
        return $value;
    } else {
        $date_parts = explode('.', $value);
        $this->attributes['dismissal_date'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
    };
  }

  public function getDismissalDateAttribute($value) {
    if($value == Null){
        return $value;
    } else {
      $date_parts = explode('-', $value);
      $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
      return $value;
    };
  }
  /**
  * Получаем вакансию для сотрудников.
  */
  public function staffer()
  {
    return $this->belongsTo('App\Staffer');
  }
  /**
  * Получаем сотрудника.
  */
  public function user()
  {
    return $this->belongsTo('App\User');
  }
  /**
  * Получаем автора
  */
   public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }
}
