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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


// Фильтры
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class GoodsCategory extends Model
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

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'seo_description',
        'photo_id',
        'parent_id',
        'goods_mode_id',
        'category_status',
        'category_id',
        'author_id',
        'editor_id',
    ];

    // Получаем компании.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function goods_products()
    {
        return $this->hasMany('App\GoodsProduct');
    }

    public function goods_mode()
    {
        return $this->belongsTo('App\GoodsMode');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Получаем метрики
    // public function metrics()
    // {
    //     return $this->belongsToMany('App\Metric', 'metric_entity', 'entity_id', 'metric_id')->where('entity', 'goods_categories');
    // }
    public function one_metrics()
    {
        return $this->morphToMany('App\Metric', 'metric_entity')->where('set_status', 'one');
    }

    public function set_metrics()
    {
        return $this->morphToMany('App\Metric', 'metric_entity')->where('set_status', 'set');
    }

    // Состав (только сырье)
    public function compositions()
    {
        return $this->morphedByMany('App\RawsArticle', 'compositions');
    }

    // public function compositions()
    // {
    //     return $this->belongsToMany('App\Raw', 'compositions', 'goods_category_id', 'entity_id')->where('entity', 'raws');;
    // }

    // --------------------------------------- Запросы -----------------------------------------
    public function getIndex($answer, $request)
    {
        return $this->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны альбомов
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
    }

    public function getItem($answer, $id)
    {
        return $this->moderatorLimit($answer)->findOrFail($id);
    }
}
