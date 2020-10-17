<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldLead extends Model
{
    protected $table = 'leads';
    protected $connection = 'mysql2';

    public $timestamps = false;
    
    // Комментарии
    // public function comments()
    // {
    //     return $this->hasMany('App\OldHistory', 'id_lead');
    // }
    
    // // Рекламации
    // public function claims()
    // {
    //     return $this->hasMany('App\OldClaim', 'id_lead');
    // }
    
    // // Менеджер
    // public function manager()
    // {
    //     return $this->belongsTo('App\OldUser', 'id_manager');
    // }
    
    // // Задача
    // public function task()
    // {
    //     return $this->belongsTo('App\OldTask', 'id_task');
    // }
    
    // // Этап
    // public function stage()
    // {
    //     return $this->belongsTo('App\OldStage', 'id_stage');
    // }
    
    // // Город
    // public function city()
    // {
    //     return $this->belongsTo('App\OldCity', 'id_city');
    // }
    
    // // Выбор
    // public function service()
    // {
    //     return $this->belongsTo('App\OldService', 'id_service');
    // }
    
    // // Задачи
    // public function challenges()
    // {
    //     return $this->hasMany('App\OldChallenge', 'id_lead');
    // }

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
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
        return $this->morphToMany('App\Phone', 'phone_entity')
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
    public function estimates()
    {
        return $this->hasMany('App\Models\System\Documents\Estimate');
    }

    // Основной заказ
    public function main_estimates()
    {
        return $this->hasMany('App\Models\System\Documents\Estimate')->whereNull('draft');
    }

    // Текущий заказ
    public function getEstimateAttribute()
    {
        if(!empty($this->main_estimates->first()))
        {
            $value = $this->main_estimates->first();
        } else {
            $value = null;
        }
        return $value;
    }
}
