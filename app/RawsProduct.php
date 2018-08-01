<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class RawsProduct extends Model
{
     // Включаем кеш
    use Cachable;

    // Получаем категорию
    public function raws_category()
    {
        return $this->belongsTo('App\RawsCategory');
    }

     // Получаем категорию
    public function raws()
    {
        return $this->hasMany('App\Raw');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Получаем еденицу измерения
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    

    // Альбом
    public function album()
    {
        return $this->belongsTo('App\Album');
    }
}
