<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Template extends BaseModel
{
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'description',

        'path',
        'html',

        'category_id',

        'display',
        'system',
        'moderation'
    ];
}
