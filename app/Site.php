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
// use App\Scopes\Filters\DateIntervalFilter;

class Site extends Model
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
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'domain',
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

    // Страницы
    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    // Навигации
    public function navigations()
    {
        return $this->hasMany(Navigation::class);
    }

    // Плагины
    public function plugins()
    {
        return $this->hasMany(Plugin::class);
    }

    // Филиалы
    public function filials()
    {
        return $this->belongsToMany(Department::class, 'filial_site', 'site_id', 'filial_id');
    }

    // Каталоги
    // Товаров
    public function catalogs_goods()
    {
        return $this->belongsToMany(CatalogsGoods::class, 'catalogs_goods_site');
    }

    // Услуг
    public function catalogs_services()
    {
        return $this->belongsToMany(CatalogsService::class, 'catalogs_service_site');
    }

}
