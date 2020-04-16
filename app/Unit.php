<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    // use Cachable;
    use SoftDeletes;

    // Категория
    public function category()
    {
        return $this->belongsTo(UnitsCategory::class);
    }
}
