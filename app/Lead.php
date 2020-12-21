<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Documents\Estimate;
use App\Models\System\Traits\Quietlable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\Traits\ManagerTraitScopes;
use Carbon\Carbon;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


class Lead extends BaseModel
{
    use SoftDeletes,
        Cachable,
        Notifiable,
        Quietlable,
        ManagerTraitScopes;

    const ALIAS = 'leads';
    const DEPENDENCE = true;

    // public $timestamps = false;

//    protected $with = [
//        'location.city',
//        'main_phones'
//    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'shipment_at'
    ];

    protected $fillable = [
        'name',
        'user_id',

        'company_name',
        'organization_id',

        'location_id',
        'email',

        'filial_id',
        'outlet_id',

        'client_id',

        'private_status',

        'shipment_at',

        'stage_id',
        'badget',

        'order_amount_base',
        'need_delivery',

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

    public function outlet()
    {
        return $this->belongsTo('App\Outlet');
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
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

    public function getFirstChallengeAttribute()
    {
        if (!empty($this->challenges->where('status', null)->sortByDesc('deadline_date')->first())) {
            $value = $this->challenges->where('status', null)->sortByDesc('deadline_date')->first();
        } else {
            $value = null;
        };

        return $value;
    }

    public function getExpiredChallengeAttribute()
    {
        if (!empty($this->challenges->where('status', null)->sortByDesc('deadline_date')->first())) {
            $value = $this->challenges->where('status', null)->sortByDesc('deadline_date')->first();
        } else {
            $value = null;
        };

        return $value;
    }

    public function expired_challenge()
    {
        // return $this->morphMany('App\Challenge', 'challenges')->where('challenges_type_id', 2)->whereNull('status')->whereDate('deadline_date', '<=', Carbon::now()->format('Y-m-d']);

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
        if (!empty($this->main_phones->first())) {
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
    //     return $this->hasMany('App\Models\System\Documents\Estimate')->whereNull('draft');
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

    /**
     * Фильтр
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query)
    {
        $filters = $this->getFilters(self::ALIAS);

        if (isset($filters['period_date_min'])) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d.m.Y', $filters['period_date_min']));
        }
        if (isset($filters['period_date_max'])) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d.m.Y', $filters['period_date_max']));
        }

        if (isset($filters['shipment_date_min'])) {
            $query->whereDate('shipment_at', '>=', Carbon::createFromFormat('d.m.Y', $filters['shipment_date_min']));
        }
        if (isset($filters['shipment_date_max'])) {
            $query->whereDate('shipment_at', '<=', Carbon::createFromFormat('d.m.Y', $filters['shipment_date_max']));
        }

        if (isset($filters['status'])) {
            if ($filters['status'] == 'fiz') {
                $query->whereNull('company_name');
            }
            if ($filters['status'] == 'ur') {
                $query->whereNotNull('company_name');
            }
        }

        if (isset($filters['goods'])) {
            $query->whereHas('estimate', function ($q) use ($filters) {
                foreach ($filters['goods'] as $id) {
                    $q->whereHas('goods_items', function ($q) use ($id) {
                        $q->where('goods_id', $id);
//                    $q->whereIn('goods_id', $filters['goods']);
                    });
                }
            });
        }

        if (isset($filters['challenges'])) {
            if ($filters['challenges'] == true) {
                $query->where('challenges_active_count', '>', 0);
            } else {
                $query->where('challenges_active_count', 0);
            }
        }

        if (isset($filters['cities'])) {
            $query->whereHas('location', function ($q) use ($filters) {
                $q->whereIn('city_id', $filters['cities']);
            });
        }

        if (isset($filters['managers'])) {
            $query->whereIn('manager_id', $filters['managers']);
        }

        if (isset($filters['lead_methods'])) {
            $query->whereIn('lead_method_id', $filters['lead_methods']);
        }

        if (isset($filters['lead_types'])) {
            $query->whereIn('lead_type_id', $filters['lead_types']);
        }

        if (isset($filters['sources'])) {
            $query->whereIn('source_id', $filters['sources']);
        }

        if (isset($filters['stages'])) {
            $query->whereIn('stage_id', $filters['stages']);
        }


        if (isset($filters['estimate_total_min'])) {
            $query->whereHas('estimate', function ($q) use ($filters) {
                $q->where('total', '>=', $filters['estimate_total_min']);
            });
        }

        if (isset($filters['estimate_total_max'])) {
            $query->whereHas('estimate', function ($q) use ($filters) {
                $q->where('total', '<=', $filters['estimate_total_max']);
            });
        }

        // if (isset($filters['prepayment_min'])) {
        //     $query->whereHas('estimate', function ($q) use ($filters) {
        //         $q->where('paid', '>=', $filters['prepayment_min'])
        //         ->where('total', '>', 'paid');
        //     });
        // }

        // if (isset($filters['prepayment_max'])) {
        //     $query->whereHas('estimate', function ($q) use ($filters) {
        //         $q->where('paid', '<=', $filters['prepayment_max'])
        //         ->where('total', '>', 'paid');
        //     });
        // }


        return $query;
    }

    // this is a recommended way to declare event handlers
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($lead) { // before delete() method call this
            $lead->estimate->goods_items()->each(function ($item) {
                $item->delete();
            });
            $lead->estimate()->delete();
            // do the rest of the cleanup...
        });
    }

}
