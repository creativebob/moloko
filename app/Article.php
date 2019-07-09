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

class Article extends Model
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
        'description',

        'manufacturer_id',
        'articles_group_id',

        'internal',
        'manually',
        'external',

        'cost_default',
        'cost_mode',
        'price_default',
        'price_mode',
        'price_rule_id',

        'portion_status',
        'portion_name',
        'portion_abbreviation',
        'portion_count',

        'draft',
        'weight',
        'unit_id',

        'kit',
        'video_url',
    ];

    // Группа
    public function group()
    {
        return $this->belongsTo(ArticlesGroup::class, 'articles_group_id');
    }

    // Товар
    // public function goods()
    // {
    //     return $this->hasMany(Goods');
    // }

    // Состав
    public function raws()
    {
        return $this->belongsToMany(Raw::class, 'article_raw')
            ->withPivot([
                'value',
                'use',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Товары
    public function goods()
    {
        return $this->belongsToMany(Goods::class, 'article_goods')
            ->withPivot([
                'value'
            ]);
    }

    // Состав (набор)
    // public function set_compositions()
    // {
    //     return $this->morphedByMany(Article', 'articles_values')->withPivot('value');
    // }

    // Производитель
    public function manufacturer()
    {
        return $this->belongsTo(Company::class);
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

    // Товар
    public function cur_goods()
    {
        return $this->hasOne(Goods::class);
    }

    // Еденица измерения
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Вес
    public function getWeightAttribute($value)
    {
        if (isset($this->unit_id)) {
            $weight = $value / $this->unit->ratio;
        } else {
            $weight = $value / $this->group->unit->ratio;
        }

        return $weight;
    }
}
