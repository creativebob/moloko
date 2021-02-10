<?php

namespace App;

use App\Models\System\Traits\Archivable;
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
use App\Scopes\Traits\AgentsTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Agent extends Model
{

    use Cachable,
        Archivable;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use AgentsTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;

    protected $fillable = [
        'agent_id',
        'agent_type_id',
        'description',

        'display',
        'system',
        'moderation'
    ];

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company', 'agent_id');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем признак агента
    public function agent_type()
    {
        return $this->belongsTo(AgentType::class);
    }

//    public function agent()
//    {
//        return $this->belongsTo('App\Company');
//    }

    public function schemes()
    {
        return $this->belongsToMany(AgencyScheme::class, 'agent_scheme', 'agent_id', 'agency_scheme_id');
    }

    public function goodsSchemes()
    {
        return $this->belongsToMany(AgencyScheme::class, 'agent_scheme', 'agent_id', 'agency_scheme_id')
            ->where('catalog_type', 'App\CatalogsGoods');
    }

    public function servicesSchemes()
    {
        return $this->belongsToMany(AgencyScheme::class, 'agent_scheme', 'agent_id', 'agency_scheme_id')
            ->where('catalog_type', 'App\CatalogsService');
    }

}
