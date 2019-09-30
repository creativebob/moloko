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

    // Артикул
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // Категория
    public function category()
    {
        return $this->belongsTo(RawsCategory::class);
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

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'entity_metric_value')
            ->withPivot('value');
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

    // Еденица измерения
    public function unit_for_composition()
    {
        return $this->belongsTo(Unit::class, 'unit_for_composition_id');
    }

    // Еденица измерения
    public function unit_portion_goods()
    {
        return $this->belongsTo(Unit::class, 'unit_portion_goods_id');
    }

    // Получаем себестоимость
    // public function cost()
    // {s
    //     return $this->morphMany(Cost::class, 'cmv');
    // }

    // Получаем себестоимость
    // public function getCostAttribute()
    // {
    //     if($this->article->manufacturer_id){
    //         return $this->morphMany(Cost::class, 'cmv')->where('manufacturer_id', $this->article->manufacturer_id)->first()->average;
    //     } else {
    //         return 0;
    //     }
    // }

    // Геттер: Функция получения веса в кг. учитывая все надстройки и переопределения в еденицах измерения
    public function getWeightAttribute($value)
    {
        // Расчет если есть порции
        if($this->portion_goods_status){
            return $this->article->weight / $this->article->unit->ratio * $this->portion_goods_count * $this->unit_portion_goods->ratio;
        } else {

            // Расчет если указано в штуках
            if($this->article->unit_id == 32){
                return $this->article->weight * $this->article->unit_weight->ratio;

            // Расчет если в единицах
            } else {
                return $this->article->weight;
            }
        }
    }

    // Получаем себестоимость
    public function getCostPortionAttribute()
    {

        // Существует ли запись на складе
        if($this->morphMany(Cost::class, 'cmv')->first() !== null){

            if($this->article->manufacturer_id){
                
                if($this->portion_goods_status){
                    return $this->morphMany(Cost::class, 'cmv')->where('manufacturer_id', $this->article->manufacturer_id)->first()->average * $this->unit_portion_goods->ratio * $this->portion_goods_count;
                } else {
                    return $this->morphMany(Cost::class, 'cmv')->where('manufacturer_id', $this->article->manufacturer_id)->first()->average;
                }

            } else {
                return 0;
            }
        } else {

            return 0;
        }

    }


}
