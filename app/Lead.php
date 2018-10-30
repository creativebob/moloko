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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;
use App\Scopes\Filters\ValueFilter;

class Lead extends Model
{

    // Включаем кеш
    use Cachable;

    use Notifiable;
    // use SoftDeletes;

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

    // public $timestamps = false;

    protected $dates = ['deleted_at', 'created_at'];
    protected $fillable = [
        'name', 
    ];


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
    public function choices_goods_categories()
    {
        return $this->morphedByMany('App\GoodsCategory', 'choices');
    }

    // Получаем тип обращения
    public function choices_services_categories()
    {
        return $this->morphedByMany('App\ServicesCategory', 'choices');
    }

    // Получаем тип обращения
    public function choices_raws_categories()
    {
        return $this->morphedByMany('App\RawsCategory', 'choices');
    }

    // Получаем менеджера
    public function manager()
    {
        return $this->belongsTo('App\User', 'manager_id');
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
        return $this->morphMany('App\Challenge', 'challenges');
    }

    // Получаем активные задачи
    public function challenges_active()
    {
        return $this->morphMany('App\Challenge', 'challenges')->whereNull('status');
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

        return $this->morphOne('App\Challenge', 'challenges')->where('challenges_type_id', 2)->whereNull('status')->oldest('deadline_date');
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

    // Заказы
    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    // Основной заказ
    public function main_orders()
    {
        return $this->hasMany('App\Order')->whereNull('draft');
    }

    // Текущий заказ
    public function getOrderAttribute()
    {
        if(!empty($this->main_orders->first()))
        {
            $value = $this->main_orders->first();
        } else {
            $value = null;
        }
        return $value;
    }

}