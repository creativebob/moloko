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

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name', 
        'alias', 
        'phone', 
        'extra_phone'
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

    // Связываем компанию с информацией о ее директоре
    public function director()
    {
        return $this->BelongsTo('App\User', 'director_user_id');
    }

    public function departments()
    {
        return $this->hasMany('App\Department');
    }

    public function filials()
    {
        return $this->hasMany('App\Department')->where('filial_status', 1);
    }

    public function sites()
    {
        return $this->hasMany('App\Site');
    }

    public function users()
    {
        return $this->hasMany('App\User');
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

    public function schedules()
    {
        return $this->belongsToMany('App\Schedule', 'schedule_entity', 'entity_id', 'schedule_id')->where('entity', 'companies');
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

    // Получаем контрагентов
    public function suppliers()
    {
        return $this->hasMany('App\Supplier', 'contragent_id');
    }

    // Получаем контрагентов
    public function manufacturers()
    {
        return $this->belongsToMany('App\Company', 'manufacturers', 'company_id', 'contragent_id');
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

    // // Получаем клиентов
    // public function clients()
    // {   
    //     return $this->belongsToMany('App\Company', 'suppliers', 'company_id', 'supplier_id')->where('client_status', 1);
    // }

    // // Получаем поставщиков
    // public function vendors()
    // {
    //     return $this->belongsToMany('App\Company', 'suppliers', 'company_id', 'supplier_id')->where('vendor_status', 1);
    // }

    // // Получаем производителей
    // public function manufacturers()
    // {
    //     return $this->belongsToMany('App\Company', 'suppliers', 'company_id', 'supplier_id')->where('manufacturer_status', 1);
    // }

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
