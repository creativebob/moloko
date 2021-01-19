<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class CatalogsService extends BaseModel
{
    use Cachable,
        SoftDeletes;
    const ALIAS = 'catalogs_services';
    const DEPENDENCE = false;

    protected $dates = [
        'deleted_at'
    ];

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

    public function prices_services()
    {
        return $this->hasMany(PricesService::class);
    }

    // Филиалы
    public function filials()
    {
        return $this->belongsToMany(Department::class, 'catalogs_service_filial', 'catalogs_service_id', 'filial_id');
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

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_catalog_services', 'catalog_services_id', 'outlet_id');
    }

    // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();
        self::deleting(function($catalogs_service) { // before delete() method call this
            $catalogs_service->filials()->detach();
        });
    }
}
