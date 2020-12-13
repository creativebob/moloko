<?php

namespace App;

use App\Models\System\Traits\Archivable;
use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyScheme extends Model
{
    use Commonable,
        Archivable,
        SoftDeletes,
        Cachable;

    protected $dates = [
        'archived_at',
    ];

    protected $fillable = [
        'name',
        'description',

        'catalog_id',
        'catalog_type',

        'percent_default',

        'display',
        'system',
        'moderation'
    ];

    public function catalog()
    {
        return $this->morphTo();
    }
}
