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

class Right extends Model
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
      'right_name', 
		  'right_action', 
		  'category_right_id', 
    ];


  public function roles()
  {
    return $this->belongsToMany('App\Role')->withPivot('category_right_id', 'directive', 'object_entity', 'right_name');
  }

  public function сategory_right()
  {
    return $this->belongsTo('App\Сategory_right');
  }

  public function actionentity()
  {
    return $this->hasOne('App\ActionEntity', 'id', 'object_entity');
  }

}
