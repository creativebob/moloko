<?php

namespace App\Models\System\Parser;

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

    protected $dates = [
        'date',
    ];

    protected $fillable = [
        'date',
        'number',

        'client_id',

        'debit',
        'paid',
        'amount',

        'display',
        'system',
        'moderation',

        'created_at',
        'company_id',
        'author_id'
    ];
}
