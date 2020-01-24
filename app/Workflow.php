<?php

namespace App;

use App\Models\System\Traits\Commonable;
use App\Models\System\Traits\Processable;
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
use App\Scopes\Traits\SuppliersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class Workflow extends Model
{

	// Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    use Commonable;
    use Processable;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use SuppliersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;

    protected $fillable = [
        'category_id',
        'process_id',

        'display',
        'system',
        'moderation'
    ];

    // Процесс
    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    // Категория
    public function category()
    {
        return $this->belongsTo(WorkflowsCategory::class);
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Process::class, 'process_workflow')
        ->where('draft', false)
        ->whereHas('service', function($q) {
            $q->where('archive', false);
        });
    }

}
