<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    use Publicable;
    use Cachable;
    use SoftDeletes;

    protected $appends = [
        'rest'
    ];

    protected $with = [
        'article',
        'metrics'
    ];

    protected $morphClass = 'goods';

    public function getRestAttribute()
    {
        if(!empty($this->hasMany('App\GoodsStock', 'cmv_id')->where('filial_id', 1)->first())){
            return $this->hasMany('App\GoodsStock', 'cmv_id')->where('filial_id', 1)->first()->free;
        } else {
            return null;
        }
    }

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
        return $this->belongsToMany('App\Metric', 'goods_metric')
            ->withPivot('value');
    }

    public function prices()
    {
        return $this->hasMany(PricesGoods::class)
            ->archive();
    }

    public function related()
    {
        return $this->belongsToMany(Goods::class, 'goods_related', 'goods_id', 'related_id')
            ->display()
            ->archive();
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
