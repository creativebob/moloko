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

class MenuSite extends Model
{

	// Подключаем Scopes для главного запроса
  	use CompaniesLimitTraitScopes;
	use AuthorsTraitScopes;
	use SystemitemTraitScopes;
	use FilialsTraitScopes;
	use TemplateTraitScopes;
	use ModeratorLimitTraitScopes;
	
    protected $table = 'menu_site';
    protected $fillable = [
    	'menu_id', 
		'site_id', 
    ];

    
}
