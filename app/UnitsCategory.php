<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class UnitsCategory extends Model
{
    // use Cachable;
    use SoftDeletes;

    // Категория едениц измерения
    public function units()
    {
        return $this->hasMany(Unit::class, 'category_id');
    }
}
