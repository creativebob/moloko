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

class List_item extends Model
{

 	use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorFilterTraitScopes;
		
  	protected $dates = ['deleted_at'];
    protected $fillable = [
	    'item_entity', 
	    'booklist_id', 
    ];

    /**
  * Получаем запись (ID списка).
  */
  public function booklist()
  {
    return $this->belongsTo('App\Booklist');
  }

}
