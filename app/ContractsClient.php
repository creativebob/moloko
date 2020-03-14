<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ContractsClient extends Model
{
    // Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    use Commonable;

    protected $fillable = [
        'date',
        'number',

        'client_id',

        'debit',
        'paid',
        'amount',

        'display',
        'system',
        'moderation'
    ];
}
