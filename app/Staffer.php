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

    protected $table = 'staff';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'position_id',
        'department_id',
        'filial_id'
    ];

    // Отдел должности.
    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    // Филиал должности.
    public function filial()
    {
        return $this->belongsTo('App\Department', 'filial_id');
    }

    // Должность
    public function position()
    {
        return $this->belongsTo('App\Position');
    }

    // Пользователь
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // Сотрудники
    public function employees()
    {
        return $this->hasMany('App\Employee');
    }

    // Текущий сотрудник
    public function employee()
    {
        return $this->hasOne('App\Employee')->whereNull('dismissal_date');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Автор
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем все графики на сотрудника
    public function schedules()
    {
        return $this->morphToMany('App\Schedule', 'schedule_entities')->withPivot('mode');
    }

    // Получаем график компании в адаптированном под шаблон виде
    public function getMainScheduleAttribute($value) {
        $main_schedule = $this->morphToMany('App\Schedule', 'schedule_entities')->with('worktimes')->wherePivot('mode', 'main')->first();
        if($main_schedule != null){
            return $main_schedule;
        } else {
            return $value;
        }
    }

    // Получаем график компании в адаптированном под шаблон виде
    public function getWorktimeAttribute($value) {
        $worktime = $this->morphToMany('App\Schedule', 'schedule_entities')->wherePivot('mode', 'main')->first();
        if($worktime != null){
            $worktime = $worktime->worktimes;
            return worktime_to_format($worktime->keyBy('weekday'));
        } else {
            return $value;
        }
    }

    // Получаем
    public function worktime()
    {
        return $this->hasMany('App\Worktime');
    }

    // --------------------------------------- Запросы -----------------------------------------
    public function getIndex($request, $answer)
    {
        return $this->with('filial', 'department', 'user.main_phones', 'position', 'employee', 'company.filials')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
        ->filter($request, 'position_id')
        ->filter($request, 'department_id')
        ->dateIntervalFilter($request, 'date_employment')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
    }

}
