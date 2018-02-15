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

class Position extends Model
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
     * Загрузка типажа мягкого удаления для модели.
     *
     * @return void
     */
    // public static function bootModeration()
    // {
    //   static::addGlobalScope(new ModerationScope);
    // }


    //   /**
    //  * The "booting" method of the model.
    //  *
    //  * @return void
    //  */
    //  
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ModerationScope);
    }
  

  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'position_name',
    'page_id',
    'direct_status',
    'company_id',
  ];
  /**
   * Получаем районы и города области.
   */
  public function page()
  {
    return $this->belongsTo('App\Page');
  }
  /**
   * Получаем сотрудников должности.
   */
  public function employees()
  {
    return $this->hasMany('App\Employee');
  }

    /**
  * Получаем роли.
  */
  public function roles()
  {
    return $this->belongsToMany('App\Role');
  }
  /**
  * Получаем автора
  */
   public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }
}
