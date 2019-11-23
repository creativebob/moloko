<?php

namespace App;

use App\Models\Traits\Commonable;
use Carbon\Carbon;
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

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class Promotion extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    use Commonable;

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

    protected $dates = [
        'deleted_at',
        'begin_date',
        'end_date'
    ];

    protected $fillable = [
        'name',
        'description',
        'begin_date',
        'end_date',

        'display',
        'system',
        'moderation'
    ];

    public function setBeginDateAttribute($value)
    {
        $this->attributes['begin_date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function setEndDateAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['end_date'] = Carbon::createFromFormat('d.m.Y', $value);
        }
    }
}
