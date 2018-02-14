<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\ModerationScope;

use App\Scopes\Traits\CompaniesFilterTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemitemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
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
	    'booklist_name', 
	    'booklist_description', 
    ];

  public function list_items()
  {
    return $this->hasMany('App\List_item');
  }


  public function users()
  {
    return $this->belongsToMany('App\User');
  }

    /**
  * Получаем компанию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }

  /**
  * Получаем автора
  */
   public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }

    /**
  * Получаем сущность.
  */
  public function entity()
  {
    return $this->belongsTo('App\Entity');
  }


}
