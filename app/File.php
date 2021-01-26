<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class File extends BaseModel
{
    use Cachable;

    protected $fillable = [
        'name',
        'title',
        'description',
        'slug',

        'path',

        'size',
        'extension',

        'display',
        'system',
        'moderation'
    ];

    public function vendors()
    {
        return $this->morphedByMany(Vendor::class, 'entity', 'file_entities');

    }
}
