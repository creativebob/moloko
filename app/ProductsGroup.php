<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductsGroup extends Model
{
    // Получаем категорию
    public function products_category()
    {
        return $this->belongsTo('App\ProductsCategory');
    }
}
