<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class DisplayMode extends Model
{
    // Включаем кеш
    use Cachable;
}
