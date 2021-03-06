<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;


    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'filial_id',
        'begined_at',
        'ended_at',
        'sources_id',
        'utm',

        'display',
        'system',
        'moderation'
    ];

}
