<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Dispatch extends BaseModel
{

    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'entity_type',
        'entity_id',

        'mailing_id',

        'email',

        'is_delivered',
        'is_opened',
        'is_spamed',

        'display',
        'system',
        'moderation'
    ];
}
