<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    use Publicable;
    use Cachable;

    protected $with = [
        'article',
        'metrics'
    ];

    protected $morphClass = 'goods';

    // Артикул
    public function article()
    {
        return $this->belongsTo(Article::class)
//            ->display()
            ->draft();
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany('App\Metric', 'entity', 'entity_metric_value')
            ->withPivot('value');
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

}
