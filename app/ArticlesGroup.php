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
        'company_id',
        'name',
        'photo_id',
        'stauts',
        'description',
        'unit_id',
        'rule_id',
        'album_id',
        'author_id',
        'editor_id',
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
        return $this->morphedByMany(GoodsCategory::class, 'articles_group_entity');
    }

    // Сырья
    public function raws_categories()
    {
        return $this->morphedByMany(RawsCategory::class, 'articles_group_entity');
    }

    // Помещенй
    public function rooms_categories()
    {
        return $this->morphedByMany(RoomsCategory::class, 'articles_group_entity');
    }
}