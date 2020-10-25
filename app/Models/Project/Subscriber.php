<?php

namespace App\Models\Project;

use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Subscriber extends Model
{
    use Publicable;
    use Cachable;
    use SoftDeletes;

    protected $fillable = [
        'is_allowed',
        'is_active',
    ];

}
