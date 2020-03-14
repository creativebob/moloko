<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Favourite extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    use Commonable;

    protected $dates = [
        'deleted_at',
        'employment_date',
        'dismissal_date'
    ];

    protected $fillable = [
        'staffer_id',
        'user_id',
        'employment_date',
        'dismissal_date',
        'dismissal_description',

        'display',
        'system',
        'moderation'
    ];

    public function entity()
    {
        return $this->morphTo();
    }


}
