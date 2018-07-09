<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleValue extends Model
{
    // protected $table = 'article_values';
     public function value()
    {
        return $this->belongsTo('App\MetricValue', 'metric_id');
    }
}
