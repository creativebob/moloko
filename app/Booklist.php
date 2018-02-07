<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Scopes\ModerationScope;

use App\Scopes\Traits\CompaniesFilterTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\ModerationTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Booklist extends Model
{

    use Notifiable;
    use SoftDeletes;

    // Подключаем Scopes для главного запроса
    use CompaniesFilterTraitScopes;
    use AuthorsTraitScopes;
    use SystemitemTraitScopes;
    use FilialsTraitScopes;
    use ModeratorFilterTraitScopes;
		
  	protected $dates = ['deleted_at'];
    protected $fillable = [
	    'list_name', 
	    'list_description', 
    ];

  public function list_items()
  {
    return $this->hasMany('App\List_item');
  }

    /**
  * Получаем пользователей
  */
  public function users()
  {
    return $this->belongsToMany('App\User');
  }

}
