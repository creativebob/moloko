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

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'alias',
        'slug',

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

    public function price_goods()
    {
        return $this->hasMany(PricesGoods::class);
    }

    // Сайты
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'catalogs_goods_site');
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
}
