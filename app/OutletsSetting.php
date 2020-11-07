<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class OutletsSetting extends Model
{
    use Cachable;

    public $timestamps = false;
}
