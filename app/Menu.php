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

class Menu extends Model
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
