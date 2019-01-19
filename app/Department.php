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
// use App\Scopes\Filters\DateIntervalFilter;


class Department extends Model
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
    // use DateIntervalFilter;

    // Фильтрация для показа системных записей
    public function scopeOnlyFilial($query)
    {
        return $query->where('parent_id', null);
    }

    public function getPhone($value) {

        if(strlen($value) == 11 ){
            if(mb_substr($value, 0, 4) == "8395"){
                $rest1 = mb_substr($value, 5/2, 2); // возвращает "abcd"
                $rest2 = mb_substr($value, 7/2, 2); // возвращает "abcd"
                $rest3 = mb_substr($value, 9/2, 2); // возвращает "abcd"
                $result = $rest1."-".$rest2."-".$rest3;
            } else {
                // $value = strtolower($value, "UTF-8");
                $rest1 = mb_substr($value, 0, 1); // возвращает "bcdef"
                $rest2 = mb_substr($value, 1, 3); // возвращает "bcd"
                $rest3 = mb_substr($value, 4, 3); // возвращает "abcd"
                $rest4 = mb_substr($value, 7, 2); // возвращает "abcdef"
                $rest5 = mb_substr($value, 9, 2); // возвращает "abcdef"
                $result = $rest1." (".$rest2.") ".$rest3."-".$rest4."-".$rest5;
            };
        };

        if(strlen($value) < 6){
            $result = "Номер не указан";
        };

        return $result;
    }

    public function setPhone($value) {
        $ptn = "/[^0-9]/";
        $rpltxt = "";
        $result = preg_replace($ptn, $rpltxt, $value);
        return $result;
    }

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id',
        'location_id',
        'name',
        'parent_id',
        'filial_id',
        'author_id',
        'display',
        'system-item',
    ];

    // Вложенные
    public function childs()
    {
        return $this->hasMany('App\Department', 'parent_id');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Штат
    public function staff()
    {
        return $this->hasMany('App\Staffer');
    }

    // // Получаем вложенные отделы
    // public function childs()
    // {
    //     return $this->hasMany('App\Department', 'parent_id', 'id');
    // }

    // Роли ???
    public function roles()
    {
        return $this->hasMany('App\Role');
    }

    // Пользователи
    public function users()
    {
        return $this->hasMany('App\User', 'filial_id');
    }

    // Локация
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

     // Графики
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

    // Телефоны

    // Основной
    public function main_phone()
    {
        return $this->morphToMany('App\Phone', 'phone_entity')
        ->wherePivot('main', '=', 1)
        ->whereNull('archive')
        ->withPivot('archive');
    }

    public function getMainPhoneAttribute()
    {
        return $this->main_phone()->first();
    }

    // Дополнительные
    public function extra_phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity')->whereNull('archive')->whereNull('main')->withPivot('archive');
    }

    // Все
    public function phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity');
    }

}