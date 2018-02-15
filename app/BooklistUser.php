<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Фильтры
use App\Scopes\ModerationScope;
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class BooklistUser extends Model
{
  // Подключаем Scopes для главного запроса
  use CompaniesLimitTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;
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
