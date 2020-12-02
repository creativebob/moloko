<?php

namespace App;

use App\Models\System\Traits\Quietlable;
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

    use SoftDeletes;
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
    // use DateIntervalFilter;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'email',

        'location_id',
        'parent_id',
        'filial_id',

        'code_map',

        'display',
        'system',
        'moderation'
    ];

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

    // Склады
    public function Stocks()
    {
        return $this->hasMany(Stock::class);
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
    public function main_phones()
    {
        return $this->morphToMany(Phone::class, 'phone_entity')
            ->wherePivot('main', '=', 1)
            ->whereNull('archive')
            ->withPivot('archive');
    }

    public function getMainPhoneAttribute()
    {
        if(!empty($this->main_phones->first()))
        {
            $value = $this->main_phones->first();
        } else {
            $value = null;
        }
        return $value;
    }

    // Дополнительные
    public function extra_phones()
    {
        return $this->morphToMany(Phone::class, 'phone_entity')
            ->whereNull('archive')
            ->whereNull('main')
            ->withPivot('archive');
    }

    // Все
    public function phones()
    {
        return $this->morphToMany(Phone::class, 'phone_entity');
    }

    // Продвижения
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'filial_promotion', 'filial_id', 'promotion_id');
    }

    // Каталоги
    // Товаров
    public function catalogs_goods()
    {
        return $this->belongsToMany(CatalogsGoods::class, 'catalog_goods_filial');
    }

    // Услуг
    public function catalogs_services()
    {
        return $this->belongsToMany(CatalogsService::class, 'catalog_service_filial');
    }

    // Домены
    public function domains()
    {
        return $this->belongsToMany(Domain::class, 'domain_filial');
    }

    // Зона ответственности
    public function cities()
    {
        return $this->belongsToMany(City::class, 'city_filial', 'filial_id');
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

}
