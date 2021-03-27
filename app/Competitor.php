<?php

namespace App;

use App\Models\System\Traits\Archivable;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\Filter;
use App\Scopes\Traits\AgentsTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competitor extends Model
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
        'competitor_id',
        'description',

        'display',
        'system',
        'moderation'
    ];

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company', 'competitor_id');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Направления
    public function directions()
    {
        return $this->belongsToMany(Direction::class, 'direction_competitor');
    }
}
