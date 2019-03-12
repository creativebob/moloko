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
use App\Scopes\Traits\SuppliersTraitScopes;
use App\Scopes\Traits\ManufacturersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Company extends Model
{

    // Включаем кеш
    // use Cachable;

    use Notifiable;
    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use SuppliersTraitScopes;
    use ManufacturersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $model_name = ['company'];
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'alias',
        'phone',
        'extra_phone',
        'bic',
        'location_id',
        'sector_id',
        'legal_form_id'
    ];

    // Фильтрация по городу
    public function scopeAuthorFilter($query, $request)
    {

        //Фильтруем по списку городов
        if($request->author_id){
            $query = $query->whereIn('author_id', $request->author_id);
        };

        return $query;
    }

    public function departments()
    {
        return $this->hasMany('App\Department');
    }

    public function filials()
    {
        return $this->hasMany('App\Department')->whereNull('parent_id');
    }

    public function director()
    {
        return $this->hasOne('App\Staffer')->whereHas('department', function($q){
            $q->whereNull('parent_id');
        })->whereHas('position', function($q){
            $q->where('direction_status', 1);
        })->withDefault('Не найден');
    }

    public function bank_accounts()
    {
        return $this->hasMany('App\BankAccount', 'holder_id');
    }

    public function sites()
    {
        return $this->hasMany('App\Site');
    }

    public function roles()
    {
        return $this->hasMany('App\Role');
    }

    public function staff()
    {
        return $this->hasMany('App\Staffer');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем все графики на компанию
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

    public function positions()
    {
        return $this->hasMany('App\Position');
    }

    // Получаем сектор компании
    public function sector()
    {
        return $this->belongsTo('App\Sector');
    }

    // Получаем
    public function worktime()
    {
        return $this->hasMany('App\Worktime');
    }

    // Получаем локацию компании
    public function location()
    {
        return $this->belongsTo('App\Location');
    }


    // Получаем правовую форму
    public function legal_form()
    {
        return $this->belongsTo('App\LegalForm', 'legal_form_id');
    }

    // Получаем поставщиков
    public function suppliers()
    {
        return $this->belongsToMany('App\Company', 'suppliers', 'company_id', 'supplier_id');
    }

    // Получаем дилеров
    public function dealers()
    {
        return $this->hasManyTrough('App\Dealer', 'App\Client', 'company_id');
    }

    // Производители
    // public function manufacturers()
    // {
    //     return $this->belongsToMany('App\Company', 'manufacturers', 'company_id', 'manufacturer_id')->withPivot('company_id');
    // }

    public function manufacturer_supplier()
    {

    }

    public function manufacturers()
    {
        return $this->hasMany('App\Manufacturer', 'company_id');
    }

    // Производитель
    public function manufactured($company_id = null)
    {
        return $this->belongsToMany('App\Company', 'manufacturers', 'manufacturer_id', 'company_id')->where('manufacturer_id', $company_id);
    }

    // Получаем клиентов
    // public function clients()
    // {
    //     return $this->belongsToMany('App\Company', 'clients', 'company_id', 'client_id');
    // }

    // Получаем банки
    public function banks()
    {
        return $this->belongsToMany('App\Company', 'banks', 'company_id', 'bank_id');

    }

    // Получаем компании, где мы клиент
    public function we_clients()
    {
        return $this->hasMany('App\Client', 'clientable_id');
    }

    // Получаем компании, где мы поставщик
    public function we_suppliers()
    {
        return $this->hasMany('App\Supplier', 'supplier_id');
    }

    // Получаем компании, где мы дилеры
    // public function we_dealers()
    // {
    //     return $this->hasMany('App\Dealer', 'dealer_id');
    // }

    // Получаем компании, где мы производители
    public function we_manufacturers()
    {
        return $this->hasMany('App\Manufacturer', 'manufacturer_id');
    }

    // Получаем категории продукции
    public function services_categories()
    {
        return $this->hasMany('App\ServicesCategory');
    }

    // Получаем категории продукции
    public function services_products()
    {
        return $this->hasMany('App\ServicesProduct');
    }

    // Получаем типы услуг
    public function services_types()
    {
        return $this->belongsToMany('App\ServicesType', 'companies_services_types', 'company_id', 'services_type_id');
    }

    // Телефоны

    // Основной
    public function main_phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity')->wherePivot('main', '=', 1)->whereNull('archive')->withPivot('archive');
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
        return $this->morphToMany('App\Phone', 'phone_entity')->whereNull('archive')->whereNull('main')->withPivot('archive');
    }

    // Все
    public function phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity');
    }

    // Отзывы
    public function feedback()
    {
        return $this->morphMany('App\Feedback', 'feedback');
    }



}
