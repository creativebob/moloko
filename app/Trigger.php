<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Trigger extends Model
{
    // Включаем кеш
    use Cachable;

    protected $fillable = [
        'name',
        'description',
        'alias',
        'entity_id',

        'display',
        'system',
        'moderation'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
