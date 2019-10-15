<?php

namespace App;

use App\Models\Traits\Cmvable;
use App\Models\Traits\Commonable;
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

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class Attachment extends Model
{
    // Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    use Commonable;
    use Cmvable;

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

    protected $fillable = [
        'category_id',
        'article_id',
        'unit_for_composition_id',
        'price_unit_id',
        'price_unit_category_id',

        'portion_goods_status',
        'portion_goods_name',
        'portion_goods_abbreviation',
        'unit_portion_goods_id',
        'portion_goods_count',

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(AttachmentsCategory::class);
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Article::class, 'article_attachment')
            ->where('draft', false)
            ->whereHas('goods', function($q) {
                $q->where('archive', false);
            });
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(AttachmentsStock::class, 'cmv_id');
    }
}
