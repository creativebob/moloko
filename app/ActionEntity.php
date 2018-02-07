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

class ActionEntity extends Model
{
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use ModeratorFilterTraitScopes;

  protected $table = 'action_entity';


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function right()
  {
    return $this->hasOne('App\Right', 'action_entity', 'id', 'object_entity');
  }


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function entity()
  {
    return $this->belongsTo('App\Entity');
  }


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function action()
  {
    return $this->belongsTo('App\Action');
  }

}
