<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Archivable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingList extends BaseModel
{
    use Archivable,
        SoftDeletes,
        Cachable;

    protected $dates = [
        'archived_at',
    ];

    protected $fillable = [
        'name',
        'description',

        'archived_at',

        'display',
        'system',
        'moderation'
    ];

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'mailing_list_subscriber');
    }
}
