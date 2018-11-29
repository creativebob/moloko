<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class MetricEntity extends Model
{

	// Включаем кеш
    use Cachable;

    protected $table = 'metric_entity';

    //  public function entities()
    // {
    //    	return $this->morphTo();
    // }


}
