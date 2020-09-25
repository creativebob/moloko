<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Representative extends Model
{
    use Commonable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'organization_id',
        'description',

        'display',
        'system',
        'moderation'
    ];
}
