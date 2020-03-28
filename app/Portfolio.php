<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'description',

        'photo_id',

        'display',
        'system',
        'moderation'
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function items()
    {
        return $this->hasMany(PortfoliosItem::class);
    }

    // Кейсы
//    public function cases()
//    {
//        return $this->hasMany(BusinessCase::class);
//    }
}
