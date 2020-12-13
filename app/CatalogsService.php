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
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class CatalogsService extends Model
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
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'alias',
        'slug',

        'is_access_page',

        'description',
        'seo_description',

        'display',
        'system',
        'moderation'
    ];

    // Пункты
    public function items()
    {
        return $this->hasMany(CatalogsServicesItem::class);
    }

    // Публичные пункты
    public function items_public()
    {
        return $this->hasMany(CatalogsServicesItem::class)
            ->where('display', true);
    }

    public function price_services()
    {
        return $this->hasMany(PricesService::class);
    }

    // Филиалы
    public function filials()
    {
        return $this->belongsToMany(Site::class, 'catalogs_service_filial', 'catalogs_service_id', 'filial_id');
    }

    // Аавтор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function prices()
    {
        return $this->hasMany(PricesService::class)
            ->where([
                'archive' => false,
                'display' => true
            ]);
    }

    // Главные
    public function categories()
    {
        return $this->hasMany(CatalogsServicesItem::class)
        ->whereNull('parent_id');
    }

    public function agency_schemes()
    {
        return $this->morphMany(AgencyScheme::class, 'catalog');
    }

    // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();
        self::deleting(function($catalogs_service) { // before delete() method call this
            $catalogs_service->filials()->detach();
        });
    }
}
