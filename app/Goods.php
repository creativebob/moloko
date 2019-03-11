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

// use Illuminate\Support\Facades\Auth;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class Goods extends Model
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
    // use DateIntervalFilter;

    protected $fillable = [
        'company_id',
        'article_id',
        'goods_category_id',
    ];

    // Артикул
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // Категория
    public function category()
    {
        return $this->belongsTo(GoodsCategory::class, 'goods_category_id');
    }

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

    // Пункты каталога
    public function catalogs_items()
    {
        return $this->belongsToMany(CatalogsItem::class, 'catalogs_items_goods', 'goods_id', 'catalogs_item_id');
    }

    // Рабочие процессы
    public function workflows()
    {
        return $this->morphMany(Workflow::class, 'workflows');
    }

    // Составы заказов
    public function order_compositions()
    {
        return $this->morphMany(OrderComposition::class, 'order_compositions');
    }

}
