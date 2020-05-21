<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    // Включаем кеш
    use Cachable;


    protected $fillable = [
        'name',
        'description',
        'tag',
    ];
}
