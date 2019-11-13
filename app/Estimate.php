<?php

namespace App;

use App\Models\Traits\Commonable;
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

// Подключаем кеш
// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class Estimate extends Model
{

    // Включаем кеш
    // use Cachable;

    use SoftDeletes;

    use Commonable;

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
    use DateIntervalFilter;

    protected $dates = [
        'deleted_at',
        'date'
    ];
    protected $fillable = [
        'lead_id',
        'client_id',
	    'filial_id',
        'stock_id',

        'company_id',
        
        'number',
        'date',
        'description',
        'author_id',
        'draft',
        'is_saled',
        'is_reserved',
        'amount',
        'total',
        'discount',
        'discount_percent',

        'display',
        'system',
        'moderation'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
    }


    // Лид
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    // Клиент
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Товары
    public function goods_items()
    {
        return $this->hasMany(EstimatesGoodsItem::class);
    }

    // Услуги
    public function services_items()
    {
        return $this->hasMany(EstimatesServicesItem::class);
    }

    // Резервы
    public function reserves()
    {
        return $this->morphMany(Reserve::class, 'document');
    }
}
