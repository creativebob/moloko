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
    

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class Staffer extends Model
{

    // Включаем кеш
    // use Cachable;

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

    protected $table = 'staff';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'position_id',
        'department_id',
        'filial_id'
    ];

    // Получаем отдел данной должности.
    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    // Получаем Филиал данной должности.
    public function filial()
    {
        return $this->belongsTo('App\Department', 'filial_id');
    }

    // Получаем должность
    public function position()
    {
        return $this->belongsTo('App\Position');
    }

    // Получаем пользователя
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // Получаем сотрудников
    public function employees()
    {
        return $this->hasMany('App\Employee');
    }

    // Получаем компанию
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function schedules()
    {
        return $this->belongsToMany('App\Schedule', 'schedule_entity', 'entity_id', 'schedule_id')->where('entity', 'staff');
    }

}
