<?php

namespace App;

use App\Models\System\Traits\Clientable;
use App\Models\System\Traits\Locationable;
use App\Models\System\Traits\Phonable;
use Carbon\Carbon;
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
    use Notifiable;
    use SoftDeletes;
    use Cachable;

    use Locationable;
    use Phonable;
    use Clientable;

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

//    protected $with = [
//        'location',
//        'main_phones'
//    ];

    protected $model_name = [
        'company'
    ];

    protected $dates = [
        'deleted_at',
        'foundation_date'
    ];

    protected $fillable = [
        'name',
        'prename',
        'slogan',
        'name_short',
        'designation',
        'email',
        'alias',

        'inn',
        'kpp',
        'ogrn',
        'okpo',
        'okved',
        'bic',

        'seo_description',
        'about',
        'foundation_date',

        'external_control',

        'taxation_type_id',

        'location_id',
        'legal_location_id',

        'sector_id',
        'legal_form_id',
        'photo_id',

        'black_id',
        'white_id',
        'color_id',

        'points_rate',

        'display',
        'system',
        'moderation'
    ];

    // Фильтрация по городу
    public function scopeAuthorFilter($query, $request)
    {

        //Фильтруем по списку городов
        if ($request->author_id) {
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
        return $this->hasOne('App\Staffer')
            ->whereHas('department', function ($q) {
                $q->whereNull('parent_id');
            })
            ->whereHas('position', function ($q) {
                $q->where('direction', true);
            })
            ->withDefault('Не найден');
    }

    // Получение списка банковских счетов
    public function bank_accounts()
    {
        return $this->hasMany('App\BankAccount', 'holder_id');
    }

    // Получение одного основного банковского счета
    public function getBankAccountAttribute()
    {
        return $this->hasMany('App\BankAccount', 'holder_id')->where('is_main', true)->first();
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
    public function getMainScheduleAttribute($value)
    {
        $main_schedule = $this->morphToMany('App\Schedule', 'schedule_entities')->with('worktimes')->wherePivot('mode', 'main')->first();
        if ($main_schedule != null) {
            return $main_schedule;
        } else {
            return $value;
        }
    }

    // Получаем график компании в адаптированном под шаблон виде
    public function getWorktimeAttribute($value)
    {
        $worktime = $this->morphToMany('App\Schedule', 'schedule_entities')->wherePivot('mode', 'main')->first();
        if ($worktime != null) {
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


    // Получаем правовую форму
    public function legal_form()
    {
        return $this->belongsTo('App\LegalForm', 'legal_form_id');
    }

    // Получаем поставщиков
    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'supplier_id')
            ->where('archive', false);
    }

    public function suppliers()
    {
        return $this->belongsToMany('App\Company', 'suppliers', 'company_id', 'supplier_id');
    }

    // Получаем дилеров
    public function dealers()
    {
        return $this->hasManyTrough('App\Dealer', 'App\Client', 'company_id');
    }

    public function clients()
    {
        return $this->hasMany('App\Client', 'company_id');
    }

    // Получаем клиентов-компании
    public function clients_companies()
    {
        return $this->hasMany('App\Client', 'company_id')->where('clientable_type', 'App\Company');
    }

    // Получаем клиентов-компании
    public function clients_users()
    {
        return $this->hasMany('App\Client', 'company_id')->where('clientable_type', 'App\User');
    }

    // Производители
    // public function manufacturers()
    // {
    //     return $this->belongsToMany('App\Company', 'manufacturers', 'company_id', 'manufacturer_id')->withPivot('company_id');
    // }

    public function manufacturer_supplier()
    {

    }

    // Производитель
    public function manufacturer()
    {
        return $this->hasOne(Manufacturer::class, 'manufacturer_id')
            ->where('archive', false);
    }

    public function manufacturers()
    {
        return $this->hasMany(Manufacturer::class, 'company_id')
            ->where('archive', false);
    }

    public function manufactured()
    {
        return $this->belongsToMany('App\Company', 'manufacturers', 'manufacturer_id', 'company_id');
    }

    public function we_manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'id', 'manufacturer_id');
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'company_id');
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
    public function processes_types()
    {
        return $this->belongsToMany(ProcessesType::class, 'company_processes_type', 'company_id', 'processes_type_id');
    }

    // Отзывы
    public function feedback()
    {
        return $this->morphMany('App\Feedback', 'feedback');
    }


    public function getManufacturerSelfAttribute($value)
    {
        $ms = $this->hasOne('App\Manufacturer', 'manufacturer_id')
            ->where('archive', 0)
            ->first();
        if ($ms == null) {
            $value = false;
        } else {
            $value = true;
        };
        return $value;
    }

    public function getSupplierSelfAttribute($value)
    {
        $ms = $this->hasOne('App\Supplier', 'supplier_id')->where('archive', 0)->first();
        if ($ms == null) {
            $value = false;
        } else {
            $value = true;
        };
        return $value;
    }


    public function setFoundationDateAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['foundation_date'] = Carbon::createFromFormat('d.m.Y', $value);
        } else {
            $this->attributes['foundation_date'] = null;
        }
    }

    // Фото
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function white()
    {
        return $this->belongsTo(Vector::class)
            ->withDefault([
                'path' => '/img/system/svg/logo-white.svg'
            ]);
    }

    public function black()
    {
        return $this->belongsTo(Vector::class)
            ->withDefault([
                'path' => '/img/system/svg/logo-black.svg'
            ]);
    }

    public function color()
    {
        return $this->belongsTo(Vector::class)
            ->withDefault([
                'path' => '/img/system/svg/logo-color.svg'
            ]);
    }

    // Получаем аккаунты
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function rubricators()
    {
        return $this->belongsToMany(Rubricator::class, 'rubricator_site');
    }

    // Валюты
    public function currencies()
    {
        return $this->belongsToMany(Currency::class);
    }

    // Настройки
    public function settings()
    {
        return $this->belongsToMany(CompaniesSetting::class, 'company_setting', 'company_id', 'setting_id');
    }

    public function domain()
    {
        return $this->hasOne(Domain::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function site()
    {
        return $this->hasOne(Site::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Company::class, 'organizations', 'company_id', 'organization_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'organizations', 'organization_id', 'company_id');
    }

    public function representatives()
    {
        return $this->belongsToMany(User::class, 'representatives', 'organization_id');
    }

    public function subscriber()
    {
        return $this->morphOne(Subscriber::class, 'subsriberable');
    }

    public function legal_location()
    {
        return $this->belongsTo(Location::class, 'legal_location_id');
    }

}
