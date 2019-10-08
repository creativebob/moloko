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
    public function unit_portion()
    {
        return $this->belongsTo(Unit::class, 'unit_portion_id');
    }


    // Себестоимость
     public function cost()
     {
         return $this->morphOne(Cost::class, 'cmv');
     }

    // Склад
    public function stock()
    {
        return $this->hasOne(RawsStock::class, 'cmv_id');
    }


    // Геттер: Функция получения веса в кг. учитывая все надстройки и переопределения в еденицах измерения
    public function getWeightAttribute($value)
    {

            // Расчет если указано в штуках
            if($this->article->unit_id == 32){
                
                    // Расчет если есть порции
                    if($this->portion_status){
                        return $this->article->weight / $this->article->unit->ratio * $this->portion_count * $this->unit_portion->ratio;

                    } else {

                        return $this->article->weight;
                    }

            // Расчет если в единицах
            } else {

                // Расчет если есть порции
                if($this->portion_status){
                    return $this->article->weight / $this->article->unit->ratio * $this->portion_count * $this->unit_portion->ratio;
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
                
                if($this->portion_status){
                    return $this->morphMany(Cost::class, 'cmv')->where('manufacturer_id', $this->article->manufacturer_id)->first()->average * $this->unit_portion->ratio * $this->portion_count;
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

    public function getCostUnitAttribute()
    {

        // Существует ли запись на складе
        if($this->morphMany(Cost::class, 'cmv')->first() !== null){

            if($this->article->manufacturer_id){
                return $this->morphMany(Cost::class, 'cmv')->where('manufacturer_id', $this->article->manufacturer_id)->first()->average;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

}
