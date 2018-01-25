<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
   use SoftDeletes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'vacancy_id',
    'user_id',
    'date_employment',
    'date_dismissal',
  ];
   public function setDateEmploymentAttribute($value) {
    if($value == Null){
        return $value;
    } else {
        $date_parts = explode('.', $value);
        $this->attributes['date_employment'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
    };
  }
  public function getDateEmploymentAttribute($value) {
    if($value == Null){
        return $value;
    } else {
      $date_parts = explode('-', $value);
      $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
      return $value;
    };
  }
  public function setDateDismissalAttribute($value) {
    if($value == Null){
        return $value;
    } else {
        $date_parts = explode('.', $value);
        $this->attributes['date_dismissal'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
    };
  }
  public function getDateDismissalAttribute($value) {
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
