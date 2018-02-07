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
use App\Scopes\Traits\ModerationTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Navigation extends Model
{
  use SoftDeletes;
  // Фильтры
  use App\Scopes\ModerationScope;
  use App\Scopes\Traits\CompaniesFilterTraitScopes;
  use App\Scopes\Traits\AuthorsTraitScopes;
  use App\Scopes\Traits\SystemitemTraitScopes;
  use App\Scopes\Traits\FilialsTraitScopes;
  use App\Scopes\Traits\ModerationTraitScopes;
  use App\Scopes\Traits\ModeratorFilterTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
    protected $dates = ['deleted_at'];
    protected $fillable = [
    	'navigation_name',
      'site_id',
      'company_id',

    ];

    /**
  * Получаем пункты навигации.
  */
  public function menus()
  {
    return $this->hasMany('App\Menu');
  }
     /**
  * Получаем сайт навигации.
  */
  public function site()
  {
    return $this->belongsTo('App\Site');
  }
  /**
  * Получаем автора
  */
   public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }
}
