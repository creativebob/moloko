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

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;


class Department extends Model
{

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
        return $query->Where('filial_status', '1');
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
        'city_id',
        'department_name',
        'address',
        'phone',
        'department_parent_id',
        'filial_status',
        'filial_id',
    ];

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем должности.
    public function staff()
    {
        return $this->hasMany('App\Staffer');
    }

    // Получаем город.
    public function city()
    {
        return $this->belongsTo('App\City');
    }

    // Получаем роли филиала.
    public function roles()
    {
        return $this->hasMany('App\Role');
    } 

    public function users()
    {
        return $this->hasMany('App\User');
    }

    // Получаем локацию компании
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

     public function schedules()
    {
        return $this->belongsToMany('App\Schedule', 'schedule_entity', 'entity_id', 'schedule_id')->where('entity', 'departments');
    }

}