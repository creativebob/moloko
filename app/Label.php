<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends BaseModel
{
    use SoftDeletes,
        Cachable;

    protected $fillable = [
        'name',

        'is_external',
        'is_internal',

        'display',
        'system',
        'moderation'
    ];
}
