<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ReservesHistory extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    use Commonable;

    protected $dates = [
        'deleted_at',
        'begin_date',
        'end_date'
    ];

    protected $fillable = [
        'count',
        'reserve_id',

        'display',
        'system',
        'moderation'
    ];

    // Резерв
    public function reserve()
    {
        return $this->belongsTo(Reserve::class);
    }
}
