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

class Menu extends Model
{
  use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorFilterTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
    // protected $table = 'menu';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    	'menu_name',
      'menu_parent_id',
      'page_id',
      'table_id',

    ];

      /**
  * Получаем навигацию меню.
  */
  public function navigation()
  {
    return $this->belongsTo('App\Navigation');
  }
    /**
  * Получаем страницу меню.
  */
  public function page()
  {
    return $this->belongsTo('App\Page');
  }

    /**
  * Получаем сайты.
  */
  public function sites()
  {
    return $this->belongsToMany('App\Site');
  }
}
