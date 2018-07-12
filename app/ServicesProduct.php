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

}
