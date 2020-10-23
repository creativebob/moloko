<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Subscriber extends BaseModel
{
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'user_id',
        'site_id',

        'is_allowed',
        'is_active',

        'email',

        'display',
        'system',
        'moderation'
    ];

}
