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

class Right extends Model
{

    use Notifiable;
    use SoftDeletes;

    // Подключаем Scopes для главного запроса
    use CompaniesFilterTraitScopes;
    use AuthorsTraitScopes;
    use SystemitemTraitScopes;
    use FilialsTraitScopes;
    use ModeratorFilterTraitScopes;

		
    // БЛОК ОПИСАНИЯ ФИЛЬТРОВ:

    // Фильтрация для показа системных записей
    public function scopeSystemItem($query, $system_item)
    {
        if(isset($system_item)){
          return $query->where('system_item', '=', $system_item);
        } else {return $query;};
    }

  		protected $dates = ['deleted_at'];
      protected $fillable = [
      'right_name', 
		  'right_action', 
		  'category_right_id', 
    ];
      /**
  * Получаем права.
  */
  public function roles()
  {
    return $this->belongsToMany('App\Role')->withPivot('category_right_id', 'directive', 'object_entity', 'right_name');
  }

    /**
  * Получаем категорию права.
  */
  public function сategory_right()
  {
    return $this->belongsTo('App\Сategory_right');
  }

  public function actionentity()
  {
    return $this->hasOne('App\ActionEntity', 'id', 'object_entity');
  }

}
