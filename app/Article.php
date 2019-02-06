<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;
use App\Scopes\Traits\ContragentsTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
    

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class Article extends Model
{
    //
}
