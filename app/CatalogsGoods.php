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

class CatalogsGoods extends Model
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

    protected $table = 'catalogs_goods';

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'alias',
        'slug',

        'is_access_page',
        'is_check_stock',
        'is_exported_yml',

        'description',
        'seo_description',

        'display',
        'system',
        'moderation'
    ];

    // Пункты
    public function items()
    {
        return $this->hasMany(CatalogsGoodsItem::class);
    }

    // Публичные пункты
    public function items_public()
    {
        return $this->hasMany(CatalogsGoodsItem::class)
            ->where('display', true);
    }

    public function price_goods()
    {
        return $this->hasMany(PricesGoods::class);
    }

    // Филиалы
    public function filials()
    {
        return $this->belongsToMany(Department::class, 'catalogs_goods_filial', 'catalogs_goods_id', 'filial_id');
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
        return $this->hasMany(PricesGoods::class)
            ->where([
                'archive' => false,
//                'display' => true
            ]);
    }

    public function agency_schemes()
    {
        return $this->morphMany(AgencyScheme::class, 'catalog');
    }

//    public function prices()
//    {
//        return $this->hasManyThrough(PricesGoods::class, CatalogsGoodsItem::class);
//    }

    // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();
        self::deleting(function($catalogs_goods) { // before delete() method call this
            $catalogs_goods->filials()->detach();
        });
    }
}
