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

class EstimatesItem extends Model
{
    // Включаем кеш
    use Cachable;

    // use SoftDeletes;

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

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'estimate_id',
        'price_product_id',
        'price_product_type',
        'product_id',
        'product_type',
        'company_id',
        'author_id',
        'count',
        'price'
    ];

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Смета
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    // Прайс
    public function price_product()
    {
        return $this->morphTo();
    }

    // Продукт
    public function product()
    {
        return $this->morphTo();
    }
}
