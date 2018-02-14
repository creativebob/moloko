<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\ModerationScope;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Region extends Model
{
  use SoftDeletes;
  // Подключаем Scopes для главного запроса
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
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'region_name',
    'region_code',
    'region_vk_external_id',
  ];
  /**
   * Получаем районы и города области.
   */
  public function areas()
  {
    return $this->hasMany('App\Area');
  }
  public function cities()
  {
    return $this->hasMany('App\City');
  }
}
