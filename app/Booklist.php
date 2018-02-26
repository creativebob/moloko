<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class Booklist extends Model
{

    use Notifiable;
    use SoftDeletes;

    // Подключаем Scopes для главного запроса
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
		
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
