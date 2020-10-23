<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class TemplatesCategory extends BaseModel
{
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'description',
        'seo_description',

        'parent_id',
        'photo_id',

        'display',
        'system',
        'moderation'
    ];
}
