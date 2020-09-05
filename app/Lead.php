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

use App\Scopes\Traits\ManagerTraitScopes;

use Carbon\Carbon;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;
use App\Scopes\Filters\ValueFilter;
use App\Scopes\Filters\BooleanArrayFilter;

class Lead extends Model
{

    // Включаем кеш
    use Cachable;

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

    use ManagerTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    use DateIntervalFilter;
    use ValueFilter;
    use BooleanArrayFilter;

    // public $timestamps = false;

    protected $dates = [
    	'deleted_at',
	    'created_at',
	    'shipment_at'
    ];

    protected $fillable = [
        'name',
	    'shipment_at',

        'order_amount_base',
        'need_delivery',

        'client_id',

        'draft',

        'display',
        'system',
        'moderation'
    ];

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем филиал в котором был принят лид
    public function filial()
    {
        return $this->belongsTo('App\Department');
    }

    public function filials()
    {
        return $this->hasMany('App\Department')->where('filial_status', 1);
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем сектор лида
    public function sector()
    {
        return $this->belongsTo('App\Sector');
    }

    // Получаем локацию пользователя
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    // Получаем источник
    public function source()
    {
        return $this->belongsTo('App\Source');
    }

    // Получаем тип трафика
    public function medium()
    {
        return $this->belongsTo('App\Medium', 'medium_id');
    }

    // Получаем рекламную кампанию
    public function campaign()
    {
        return $this->belongsTo('App\Campaign');
    }

    // Получаем тип обращения
    public function lead_type()
    {
        return $this->belongsTo('App\LeadType');
    }

    // Получаем метод обращения
    public function lead_method()
    {
        return $this->belongsTo('App\LeadMethod');
    }

    // Получаем тип обращения
    public function choice()
    {
        return $this->morphTo();
    }

    // // Получаем тип обращения
    // public function choices_goods_categories()
    // {
    //     return $this->morphedByMany('App\GoodsCategory', 'choices');
    // }

    // // Получаем тип обращения
    // public function choices_services_categories()
    // {
    //     return $this->morphedByMany('App\ServicesCategory', 'choices');
    // }

    // // Получаем тип обращения
    // public function choices_raws_categories()
    // {
    //     return $this->morphedByMany('App\RawsCategory', 'choices');
    // }

    // Получаем менеджера
    public function manager()
    {
        return $this->belongsTo('App\User', 'manager_id');
    }

    // Получаем пользователя
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    // Получаем клиента
    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    // Получаем рекламации
    public function claims()
    {
        return $this->hasMany('App\Claim');
    }

    // Получаем этап
    public function stage()
    {
        return $this->belongsTo('App\Stage');
    }

    // Получаем комментарии
    public function notes()
    {
        return $this->morphMany('App\Note', 'notes');
    }

    // Получаем задачи
    public function challenges()
    {
        return $this->morphMany('App\Challenge', 'subject');
    }

    // Получаем активные задачи
    public function challenges_active()
    {
        return $this->morphMany('App\Challenge', 'subject')->whereNull('status');
    }

    public function getFirstChallengeAttribute() {
        if(!empty($this->challenges->where('status', null)->sortByDesc('deadline_date')->first()))
        {
            $value = $this->challenges->where('status', null)->sortByDesc('deadline_date')->first();
        } else {
            $value = null;
        };

        return $value;
    }

    public function getExpiredChallengeAttribute() {
        if(!empty($this->challenges->where('status', null)->sortByDesc('deadline_date')->first()))
        {
            $value = $this->challenges->where('status', null)->sortByDesc('deadline_date')->first();
        } else {
            $value = null;
        };

        return $value;
    }

    public function expired_challenge()
    {
        // return $this->morphMany('App\Challenge', 'challenges')->where('challenges_type_id', 2)->whereNull('status')->whereDate('deadline_date', '<=', Carbon::now()->format('Y-m-d'));

        return $this->morphOne('App\Challenge', 'subject')->where('challenges_type_id', 2)->whereNull('status')->oldest('deadline_date');
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

    // Проверка на рекламацию
    public function source_claim()
    {
        return $this->hasOne('App\Claim', 'source_lead_id');
    }

    // Заказ
    // public function order()
    // {
    //     return $this->hasOne('App\Order');
    // }

    // Заказы
    public function estimate()
    {
        return $this->hasOne(Estimate::class)
            ->where('is_main', true);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    // Основной заказ
    // public function main_estimates()
    // {
    //     return $this->hasMany('App\Estimate')->whereNull('draft');
    // }

    // Текущий заказ
    // public function getEstimateAttribute()
    // {
    //     if(!empty($this->main_estimates->first()))
    //     {
    //         $value = $this->main_estimates->first();
    //     } else {
    //         $value = null;
    //     }
    //     return $value;
    // }

    // Фильтры
    public function scopeFilter($query)
    {
        if (request('cities')) {
            $query->whereHas('location', function($q) {
                $q->whereIn('city_id', request('cities'));
            });
        }

        if (request('stages')) {
            $query->whereIn('stage_id', request('stages'));
        }

        if (request('managers')) {
            $query->whereIn('manager_id', request('managers'));
        }

        if (request('lead_methods')) {
            $query->whereIn('lead_method_id', request('lead_methods'));
        }

        if (request('lead_types')) {
            $query->whereIn('lead_type_id', request('lead_types'));
        }

        if (request('period_date_min')) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d.m.Y', request()->period_date_min));
        }
        if (request('period_date_max')) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d.m.Y', request()->period_date_max));
        }

        if (request('shipment_date_min')) {
            $query->whereDate('shipment_at', '>=', Carbon::createFromFormat('d.m.Y', request()->shipment_date_min));
        }
        if (request('shipment_date_max')) {
            $query->whereDate('shipment_at', '<=', Carbon::createFromFormat('d.m.Y', request()->shipment_date_max));
        }

        if (! is_null(request('status'))) {
            if (request('status') == 'fiz') {
                $query->whereNull('company_name');
            }
            if (request('status') == 'ur') {
                $query->whereNotNull('company_name');
            }
        }

        if (request('goods')) {
            $query->whereHas('estimate', function ($q) {
                foreach (request('goods') as $id) {
                    $q->whereHas('goods_items', function ($q) use ($id) {
                        $q->where('goods_id', $id);
//                    $q->whereIn('goods_id', request('goods'));
                    });
                }
            });
        }

        if (! is_null(request('challenges'))) {
            if (request('challenges') == true) {
                $query->where('challenges_active_count', '>', 0);
            } else {
                $query->where('challenges_active_count', 0);
            }
        }

        if (request('sources')) {
            $query->whereIn('source_id', request('sources'));
        }

    }


    // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();
        self::deleting(function($lead) { // before delete() method call this
            $lead->estimate->goods_items()->each(function($item) {
                $item->delete();
             });
            $lead->estimate()->delete();
            // do the rest of the cleanup...
        });
    }

}
