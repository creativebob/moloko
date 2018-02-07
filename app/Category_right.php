<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Фильтры
use App\Scopes\ModerationScope;
use App\Scopes\Traits\CompaniesFilterTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\ModerationTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Category_right extends Model
{
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use ModeratorFilterTraitScopes;
    /**
  * Получаем права категории.
  */
  public function rights()
  {
    return $this->hasMany('App\Rights');
  }


    /**
  * Получаем роли.
  */
  public function roles()
  {
    return $this->hasMany('App\Role');
  }

  
}
