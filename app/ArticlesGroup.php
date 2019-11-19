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

use App\Scopes\Traits\ManufacturersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class ArticlesGroup extends Model
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

    use ManufacturersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $fillable = [
        'name',
        'photo_id',
        'stauts',
        'description',
        'unit_id',
        'units_category_id',
        'rule_id',
        'album_id',
    ];

    // Артикулы
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    // Альбом
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Еденица измерения
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Еденица измерения
    public function units_category()
    {
        return $this->belongsTo(UnitsCategory::class);
    }

    // Автора
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Категории
    // public function categories()
    // {
    //     return $this->morphedByMany(GoodsCategory::class, 'articles_group_entity');
    // }

    // Товаров
    public function goods_categories()
    {
        return $this->morphedByMany(GoodsCategory::class, 'entity', 'articles_group_entity');
    }

    // Сырья
    public function raws_categories()
    {
        return $this->morphedByMany(RawsCategory::class, 'entity', 'articles_group_entity');
    }

    // Упаковок
    public function containers_categories()
    {
        return $this->morphedByMany(ContainersCategory::class, 'entity', 'articles_group_entity');
    }

    // Вложений
    public function attachments_categories()
    {
        return $this->morphedByMany(AttachmentsCategory::class, 'entity', 'articles_group_entity');
    }

    // Оборудования
    public function tools_categories()
    {
        return $this->morphedByMany(ToolsCategory::class, 'entity', 'articles_group_entity');
    }

    // Помещенй
    public function rooms_categories()
    {
        return $this->morphedByMany(RoomsCategory::class, 'entity', 'articles_group_entity');
    }
}
