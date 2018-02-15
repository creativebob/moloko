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

class PositionRole extends Model
{
	// Подключаем Scopes для главного запроса
 	use CompaniesLimitTraitScopes;
  	use AuthorsTraitScopes;
 	use SystemitemTraitScopes;
 	use FilialsTraitScopes;
	use TemplateTraitScopes;
 	use ModeratorLimitTraitScopes;
 	
	protected $table = 'position_role';
    protected $fillable = [
    	'id', 
    	'position_id',
        'role_id',
        'author_id',
    ];
}
