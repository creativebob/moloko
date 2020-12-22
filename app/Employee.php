<?php

namespace App;

use App\Models\System\Traits\Quietlable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

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

class Employee extends Model
{

    // Включаем кеш
//    use Cachable;
    use Quietlable;

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
        'employment_date',
        'dismissal_date'
    ];

    protected $fillable = [
        'staffer_id',
        'user_id',

        'employment_date',

        'dismissal_date',
        'dismissal_description',

        'display',
        'system',
        'moderation'
    ];

    public function setEmploymentDateAttribute($value)
    {
        $this->attributes['employment_date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function setDismissalDateAttribute($value)
    {
        $this->attributes['dismissal_date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    // Получаем вакансию для сотрудников.
    public function staffer()
    {
        return $this->belongsTo('App\Staffer');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем сотрудника.
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }


}
