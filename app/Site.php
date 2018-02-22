<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class Site extends Model
{
  
  use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use CompaniesLimitTraitScopes;
  use AuthorsTraitScopes;
  use SystemItemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'site_name',
    'site_domen',
    'company_id',
  ];
  /**
   * Получаем страницы.
   */
  public function pages()
  {
    return $this->hasMany('App\Page');
  }
    /**
  * Получаем пункты меню.
  */
  public function menus()
  {
    return $this->belongsToMany('App\Menu');
  }
  /**
   * Получаем навигации.
   */
  public function navigations()
  {
    return $this->hasMany('App\Navigation');
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
