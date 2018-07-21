<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class GoodsProduct extends Model
{
    // Включаем кеш
    use Cachable;

    // Получаем категорию
    public function goods_category()
    {
        return $this->belongsTo('App\GoodsCategory');
    }

     // Получаем категорию
    public function goods()
    {
        return $this->hasMany('App\Goods');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    

    // Альбом
    public function album()
    {
        return $this->belongsTo('App\Album');
    }
}
