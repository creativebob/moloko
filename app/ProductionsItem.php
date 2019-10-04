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

class ProductionsItem extends Model
{

    // Включаем кеш
//    use Cachable;

//    use SoftDeletes;

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
        'production_id',
        'cmv_id',
        'cmv_type',
        'cost',
        'count',
        'entity_id',
        'description',
        'stock_id',
    ];

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Родительская смета
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    // Склад
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    //
    public function cmv()
    {
        return $this->morphTo();
    }

    // сущность
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    // Списания
    public function offs()
    {
        return $this->morphMany(Off::class, 'documents_item');
    }

    // Поступления
    public function receipt()
    {
        return $this->morphOne(Receipt::class, 'documents_item');
    }

}
