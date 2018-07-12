<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicesProduct extends Model
{
    // Получаем категорию
    public function services_category()
    {
        return $this->belongsTo('App\ServicesCategory');
    }

     // Получаем категорию
    public function services()
    {
        return $this->hasMany('App\Service');
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
