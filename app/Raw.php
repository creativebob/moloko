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

class Raw extends Model
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

        'portion_status',
        'portion_name',
        'portion_abbreviation',
        'unit_portion_id',
        'portion_count',

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(RawsCategory::class);
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Article::class, 'article_raw')
        ->where('draft', false)
        ->whereHas('goods', function($q) {
            $q->where('archive', false);
        });
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(RawsStock::class, 'cmv_id');
    }

}
