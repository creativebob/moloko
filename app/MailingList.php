<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingList extends BaseModel
{
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'description',

        'display',
        'system',
        'moderation'
    ];
}
