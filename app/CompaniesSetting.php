<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class CompaniesSetting extends Model
{
    use Cachable;

    public $timestamps = false;
}
