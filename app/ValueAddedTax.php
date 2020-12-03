<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValueAddedTax extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'tag',
        'description',
    ];

}
