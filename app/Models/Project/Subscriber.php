<?php

namespace App\Models\Project;

use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Subscriber extends Model
{
    use Publicable,
        Cachable,
        SoftDeletes;

    protected $fillable = [
        'name',
        'email',

        'denied_at',
        'is_self',

        'editor_id',
    ];
}
