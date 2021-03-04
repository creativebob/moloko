<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class File extends BaseModel
{
    use Cachable;

    const RELATIONS = [
        'companies',
        'vendors',
        'domains'
    ];

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

    public function companies()
    {
        return $this->morphedByMany(Company::class, 'entity', 'file_entities');
    }

    public function domains()
    {
        return $this->morphedByMany(Domain::class, 'entity', 'file_entities');
    }
}
