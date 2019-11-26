<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    // Включаем кеш
    use Cachable;
}
