<?php

namespace App\Models\Project;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Subscriber extends BaseModel
{
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'is_allowed',
        'is_active',
    ];

}
