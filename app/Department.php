<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\ModerationScope;
use App\Scopes\Traits\CompaniesFilterTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Department extends Model
{
	use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorFilterTraitScopes;


    // Фильтрация для показа системных записей
    public function scopeOnlyFilial($query)
    {
          return $query->Where('filial_status', '1');
    }

    // Фильтрация для показа системных записей
    public function scopeSystemItem($query, $system_item)
    {
        if(isset($system_item)){
          return $query->orWhere('system_item', '1');
        } else {return $query;};
    }

        // Фильтрация для показа системных записей
    public function scopeOtherItem($query, $other_item)
    {
        if(isset($other_item)){

            if(isset($other_item['all'])){
                return $query;
            } else {
                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->WhereIn('author_id', $other_item)->orWhere('author_id', $other_item['user_id'])->orWhere('id', $other_item['user_id']);
            }
        }
    }

  
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
  	'company_id',
    'city_id',
    'department_name',
    'department_address',
    'department_phone',
    'department_parent_id',
    'filial_status',
    'filial_id',
  ];
  /**
  * Получаем компанию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }
  /**
   * Получаем должности.
   */
  public function staff()
  {
    return $this->hasMany('App\Staffer');
  }
  /**
  * Получаем город.
  */
  public function city()
  {
    return $this->belongsTo('App\City');
  }
  /**
  * Получаем роли филиала.
  */
  public function roles()
  {
    return $this->hasMany('App\Role');
  } 

}