<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogProduct extends Model
{
    public function catalog()
    {
        return $this->belongsTo('App\Catalog');
    }
}
