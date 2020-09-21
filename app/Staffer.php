<?php

namespace App;

use App\Models\System\Traits\Quietlable;
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

class Staffer extends Model
{

    // Включаем кеш
    use Cachable;
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

    protected $table = 'staff';

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'user_id',
        'position_id',
        'department_id',
        'filial_id',

        'display',
        'system',
        'moderation'
    ];

    // Отдел должности.
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Филиал должности.
    public function filial()
    {
        return $this->belongsTo(Department::class);
    }

    // Должность
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // Пользователь
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Сотрудники
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function actual_employees()
    {
        return $this->hasMany(Employee::class)
            ->whereNull('dismissal_date');
    }

    // Текущий сотрудник
    public function employee()
    {
        return $this->hasOne(Employee::class)->whereNull('dismissal_date');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Получаем все графики на сотрудника
    public function schedules()
    {
        return $this->morphToMany(Schedule::class, 'schedule_entities')->withPivot('mode');
    }

    // Получаем график компании в адаптированном под шаблон виде
    public function getMainScheduleAttribute($value) {
        $main_schedule = $this->morphToMany(Schedule::class, 'schedule_entities')->with('worktimes')->wherePivot('mode', 'main')->first();
        if($main_schedule != null){
            return $main_schedule;
        } else {
            return $value;
        }
    }

    // Получаем график компании в адаптированном под шаблон виде
    public function getWorktimeAttribute($value) {
        $worktime = $this->morphToMany(Schedule::class, 'schedule_entities')->wherePivot('mode', 'main')->first();
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
        return $this->hasMany(Worktime::class);
    }

    // --------------------------------------- Запросы -----------------------------------------
    public function getIndex($request, $answer)
    {
        return $this->with([
            'filial',
            'department',
            'user.main_phones',
            'position',
            'employee',
            'company.filials'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
        ->where('archive', false)
        ->filter($request, 'position_id')
        ->filter($request, 'department_id')
        ->dateIntervalFilter($request, 'date_employment')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
    }

}
