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

class BooklistUser extends Model
{
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use ModeratorFilterTraitScopes;
	protected $table = 'booklist_user';


    /**
  * Получаем запись (ID списка).
  */
  public function booklist()
  {
    return $this->belongsTo('App\Booklist');
  }


    /**
  * Получаем запись (ID пользователя).
  */
  public function user()
  {
    return $this->belongsTo('App\User');
  }

}
