<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Archivable;
use App\Models\System\Traits\Quietlable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Staffer extends BaseModel
{
    use Archivable,
        Cachable,
        Quietlable;

    protected $table = 'staff';

    protected $dates = [
        'archived_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'position_id',
        'department_id',
        'filial_id',

        'archived_at',

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
        return $this->belongsTo(User::class)
            ->withDefault([
                'name' => 'Вакансия'
            ]);
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
    public function getMainScheduleAttribute($value)
    {
        $main_schedule = $this->morphToMany(Schedule::class, 'schedule_entities')->with('worktimes')->wherePivot('mode', 'main')->first();
        if ($main_schedule != null) {
            return $main_schedule;
        } else {
            return $value;
        }
    }

    // Получаем график компании в адаптированном под шаблон виде
    public function getWorktimeAttribute($value)
    {
        $worktime = $this->morphToMany(Schedule::class, 'schedule_entities')->wherePivot('mode', 'main')->first();
        if ($worktime != null) {
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

    public function workplaces()
    {
        return $this->belongsToMany(Workplace::class, 'workplace_staffer');
    }
}
