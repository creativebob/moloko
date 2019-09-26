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

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class ContainersConsignment extends Model
{

    // Включаем кеш
    use Cachable;

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

    protected $dates = ['deleted_at', 'receipt_date'];
    protected $fillable = [
        'supplier_id',
        'company_id',
        // 'filial_id',
        'name',
        'description',
        'amount',
        'receipt_date',
        'number',
        'stock_id',
        'author_id',
        'draft',

        'display',
        'system',
        'moderation'
    ];

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // public function filials()
    // {
    //     return $this->hasMany('App\Department')->where('filial_status', 1);
    // }

    // Автор
    public function author()
    {
        return $this->belongsTo('App\User');
    }

    // Поставщик
    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    // Склад
    public function stock()
    {
        return $this->belongsTo('App\Stock');
    }

    // Позиции в смете
    public function items()
    {
        return $this->hasMany(ContainersConsignmentsItem::class);
    }
}
