<?php

namespace App\Models\System\Parser;

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
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class Payment extends Model
{
    // Включаем кеш
//    use Cachable;

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
    use DateIntervalFilter;

    protected $dates = [
        'deleted_at',
        'date'
    ];

    protected $fillable = [
        'payments_type_id',
        'amount',

        'currency_id',

        'date',

        'contract_id',
        'contract_type',

        'document_id',
        'document_type',

        'display',
        'system',
        'moderation',

        'created_at',
        'company_id',
        'author_id'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    // Тип
    public function type()
    {
        return $this->belongsTo(PaymentsType::class, 'payments_type_id');
    }

    // Договор
    public function contract()
    {
        return $this->morphTo();
    }

    // Валюта
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
