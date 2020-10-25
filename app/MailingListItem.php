<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingListItem extends BaseModel
{
//    use SoftDeletes;
//    use Cachable;

    protected $fillable = [
        'entity_id',
        'entity_type',

        'mailing_list_id',

        'display',
        'system',
        'moderation'
    ];
}
