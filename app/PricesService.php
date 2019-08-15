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

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class PricesService extends Model
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

    // Фильтры
    use Filter;
    use BooklistFilter;

    protected $fillable = [
        'catalogs_services_item_id',
        'catalogs_service_id',
        'service_id',
        'filial_id',
        'price',
        'archive',

        'display',
        'system',
        'moderation'
    ];


    // Каталог
    public function catalog()
    {
        return $this->belongsTo(CatalogsService::class, 'catalogs_service_id');
    }

    // Пункты каталога
    public function catalogs_item()
    {
        return $this->belongsTo(CatalogsServicesItem::class, 'catalogs_services_item_id');
    }

    // Филиал
    public function filial()
    {
        return $this->belongsTo(Department::class);
    }

    // Услуга
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Предок
    public function ancestor()
    {
        return $this->belongsTo(PricesService::class);
    }

    // Последователь
    public function follower()
    {
        return $this->hasOne(PricesService::class, 'ancestor_id')
            ->where('archive', false);
    }

    // Общее отношение для товаров и услуг
    public function product()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
