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
use App\Scopes\Traits\ModerationTraitScopes;
use App\Scopes\Traits\ModeratorFilterTraitScopes;

class Action extends Model
{

 	use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use CompaniesFilterTraitScopes;
  use AuthorsTraitScopes;
  use SystemitemTraitScopes;
  use FilialsTraitScopes;
  use ModeratorFilterTraitScopes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'action_name', 
        'action_method', 
    ];

    /**
  * Получаем действия над сущностью
  */
  public function entities()
  {
    return $this->belongsToMany('App\Entity', 'action_entity', 'action_id', 'entity_id');
  }


    /**
  * Получаем полиморфную запись (ID права).
  */
  public function actionentities()
  {
    return $this->hasMany('App\ActionEntity');
  }


}

