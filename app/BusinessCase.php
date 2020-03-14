<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessCase extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'description',

        'portfolio_id',

        'photo_id',

        'display',
        'system',
        'moderation'
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Портфолио
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
