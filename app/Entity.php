<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class Entity extends Model
{

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
  	'entity_name',
  	'entity_alias',
  ];

  public function actions()
  {
    return $this->belongsToMany('App\Action', 'action_entity', 'entity_id', 'action_id');
  }

  public function actionentities()
  {
    return $this->hasMany('App\Actionentity');
  }

  public function pages()
  {
    return $this->belongsToMany('App\Page');
  }

  public function booklists()
  {
    return $this->hasMany('App\Booklist');
  }

}
