<?php

namespace App\Models\System\Traits;

use App\Article;
use App\Cost;
use App\Metric;
use App\Unit;

trait Cmvable
{

    // Артикул
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    public function article_public()
    {
        return $this->belongsTo(Article::class)
            ->where('display', true);
    }

    public function core()
    {
        return $this->belongsTo(Article::class, 'article_id');
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
    // TODO - 04.11.20 - Затычка в виде филиала пользователя
    public function cost()
    {
        return $this->morphOne(Cost::class, 'cmv')
            ->where('filial_id', auth()->user()->stafferFilialId);
    }

	public function costs()
	{
		return $this->morphMany(Cost::class, 'cmv');
	}

    // Геттер: Функция получения веса в кг. учитывая все надстройки и переопределения в еденицах измерения
    public function getWeightAttribute()
    {
        // Расчет если есть порции
        if($this->portion_status){
            return $this->article->weight / $this->article->unit->ratio * $this->portion_count * $this->unit_portion->ratio;
        } else {
            return $this->article->weight;
        }
    }

    // Геттер: Функция получения обьема в м3. учитывая все надстройки и переопределения в еденицах измерения
    public function getVolumeAttribute()
    {
        // Расчет если есть порции
        if($this->portion_status){
            return $this->article->volume / $this->article->unit->ratio * $this->portion_count * $this->unit_portion->ratio;
        } else {
            return $this->article->volume;
        }
    }

    // Геттер: из
    public function getPortionAttribute()
    {
        // Расчет если есть порции
        if($this->portion_status){
            return $this->article->unit->ratio * $this->portion_count * $this->unit_portion->ratio;
        } else {
            return $this->article->unit->ratio;
        }
    }

    // Получаем себестоимость
    public function getCostPortionAttribute()
    {

        // Существует ли запись на складе
        if($this->morphMany(Cost::class, 'cmv')->first() !== null){

            if($this->article->manufacturer_id){

                if($this->portion_status){
                    return $this->morphMany(Cost::class, 'cmv')
                            ->where('manufacturer_id', $this->article->manufacturer_id)
                            ->first()->average * $this->unit_portion->ratio * $this->portion_count;
                } else {
                    return $this->morphMany(Cost::class, 'cmv')
                        ->where('manufacturer_id', $this->article->manufacturer_id)
                        ->first()->average;
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
        if($this->costs->where('manufacturer_id', $this->article->manufacturer_id)->first() !== null){

            if($this->article->manufacturer_id){
                return $this->costs->where('manufacturer_id', $this->article->manufacturer_id)->first()->average;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

//    public function cost_unit()
//    {
//        // Существует ли запись на складе
//        if ($this->morphMany(Cost::class, 'cmv')
//                ->where('manufacturer_id', $this->article->manufacturer_id)
//                ->first() !== null){
//
//            if($this->article->manufacturer_id){
//                return $this->morphMany(Cost::class, 'cmv')
//                    ->where('manufacturer_id', $this->article->manufacturer_id)
//                    ->first()
//                    ->average;
//            } else {
//                return 0;
//            }
//        } else {
//            return 0;
//        }
//    }

    /**
     * Фильтр
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query)
    {
        $filters = $this->getFilters(self::ALIAS);

        if (isset($filters['categories'])) {
            $query->whereIn('category_id', $filters['categories']);
        }

        if (isset($filters['manufacturers'])) {
            $query->whereHas('article', function ($q) use ($filters) {
                $q->whereIn('manufacturer_id', $filters['manufacturers']);
            });
        }

        return $query;
    }
}
