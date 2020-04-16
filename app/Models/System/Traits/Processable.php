<?php

namespace App\Models\System\Traits;

use App\Cost;
use App\Metric;
use App\Process;
use App\Unit;

trait Processable
{

    // Процесс
    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function core()
    {
        return $this->belongsTo(Process::class, 'article_id');
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'entity_metric_value')
            ->withPivot('value');
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
        return $this->morphOne(Cost::class, 'cmv')->where('filial_id', \Auth::user()->stafferFilialId);
    }

//	public function costs()
//	{
//		return $this->morphMany(Cost::class, 'cmv');
//	}

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
        if($this->morphMany(Cost::class, 'cmv')->where('manufacturer_id', $this->article->manufacturer_id)->first() !== null){

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
