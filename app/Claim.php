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

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
    
use Carbon\Carbon;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class Claim extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    use DateIntervalFilter;

    // public $timestamps = false;

    protected $dates = ['deleted_at', 'created_at'];
    protected $fillable = [
        'company_id',
        'body', 'lead_id',
        'old_claim_id',
        'author_id',

        'display',
        'system',
        'moderation'
    ];


    // Получаем лида
    public function lead()
    {
        return $this->belongsTo('App\Lead');
    }

    // Получаем лида источника рекламации
    public function source_lead()
    {
        return $this->belongsTo('App\Lead', 'source_lead_id');
    }

    // Получаем менеджера
    public function manager()
    {
        return $this->belongsTo('App\User', 'manager_id');
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
