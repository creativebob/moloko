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
//    use Cachable;

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
        'slug',

        'description',

        'articles_group_id',

        'internal',
        'manually',
        'external',

        'manufacturer_id',

        'kit',

        'cost_default',
        'cost_mode',
        'price_default',
        'price_mode',
        'price_rule_id',

        'video_url',
        'video',

        'content',
        'seo_description',
        'keywords',

        'package_status',
        'package_name',
        'package_abbreviation',
        'package_count',

        'weight',
        'unit_weight_id',
        'volume',
        'unit_volume_id',

        'photo_id',

        'unit_id',
        'draft',

        'display',
        'system',
        'moderation',

        'created_at'
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
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Упаковка
    public function containers()
    {
        return $this->belongsToMany(Container::class, 'article_container')
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Вложения
    public function attachments()
    {
        return $this->belongsToMany(Attachment::class, 'article_attachment')
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    public function attachments_public()
    {

        return $this->belongsToMany(Attachment::class, 'article_attachment')
            ->where([
                'display' => true,
            ])
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Товары из которых состоит текущий артикул (Набор товаров)
    public function goods()
    {
        return $this->belongsToMany(Goods::class, 'article_goods')
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Товары на базе этого артикула
    public function in_goods()
    {
        return $this->hasMany(Goods::class, 'article_id');
    }

    // Сырье на базе этого артикула
    public function in_raws()
    {
        return $this->hasMany(Raw::class, 'article_id');
    }

    // Упаковка на базе этого артикула
    public function in_containers()
    {
        return $this->hasMany(Container::class, 'article_id');
    }

    public function in_attachments()
    {
        return $this->hasMany(Attachment::class, 'article_id');
    }

    public function in_impacts()
    {
        return $this->hasMany(Impact::class, 'article_id');
    }

    public function in_tools()
    {
        return $this->hasMany(Tool::class, 'article_id');
    }


    // Состав (набор)
    // public function set_compositions()
    // {
    //     return $this->morphedByMany(Article', 'articles_values')->withPivot('value');
    // }

    // Производитель
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
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

    public function raw()
    {
        return $this->hasOne(Raw::class);
    }

    public function tool()
    {
        return $this->hasOne(Tool::class);
    }

    public function container()
    {
        return $this->hasOne(Container::class);
    }

    public function impact()
    {
        return $this->hasOne(Impact::class);
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class);
    }

    public function room()
    {
        return $this->hasOne(Room::class);
    }

    public function codes()
    {
        return $this->hasMany(ArticleCode::class);
    }

    public function parts()
    {
        return $this->belongsToMany(Article::class, 'article_article', 'article_id', 'part_id')
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    public function owners()
    {
        return $this->belongsToMany(Article::class, 'article_article', 'part_id', 'article_id')
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Единица измерения
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Еденица измерения
    public function unit_weight()
    {
        return $this->belongsTo(Unit::class);
    }

    // Еденица измерения
    public function unit_volume()
    {
        return $this->belongsTo(Unit::class);
    }

    // Вес
    public function getWeightTransAttribute()
    {

        if (isset($this->unit_id)) {
            if (isset($this->unit_weight)) {
                $weight = $this->weight / $this->unit_weight->ratio;
            } else {
                $weight = $this->weight / $this->unit->ratio;
            }

        } else {
            $weight = $this->weight / $this->group->unit->ratio;
        }

        return $weight;
    }

    public function getWeightGramAttribute()
    {
        return $this->weight * 1000;
    }

    // Объем
    public function getVolumeTransAttribute()
    {

        if (isset($this->unit_id)) {
            if (isset($this->unit_volume)) {
                $volume = $this->volume / $this->unit_volume->ratio;
            } else {
                $volume = $this->volume / $this->unit->ratio;
            }

        } else {
            $volume = $this->volume / $this->group->unit->ratio;
        }

        return $volume;
    }
}
