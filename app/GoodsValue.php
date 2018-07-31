<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsValue extends Model
{
    // protected $table = 'goods_values';
     public function value()
    {
        return $this->belongsTo('App\MetricValue', 'metric_id');
    }
}
