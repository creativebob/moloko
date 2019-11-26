<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Trigger extends Model
{
    // Включаем кеш
    use Cachable;
}
