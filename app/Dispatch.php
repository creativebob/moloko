<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Dispatch extends BaseModel
{

    use SoftDeletes;
    use Cachable;

    protected $dates = [
        'sended_at',

        'delivered_at',
        'opened_at',
        'spamed_at',
    ];

    protected $fillable = [
        'subscriber_id',
        'mailing_id',

        'email',

        'sended_at',

        'delivered_at',
        'opened_at',
        'spamed_at',

        'company_id',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function mailing()
    {
        return $this->belongsTo(Mailing::class);
    }

    /**
     * Отправлено
     *
     * @param $query
     */
    public function scopeSended($query)
    {
        $query->whereNotNull('sended_at');
    }

    /**
     * В ожидании
     *
     * @param $query
     */
    public function scopeWaiting($query)
    {
        $query->whereNull('sended_at');
    }
}
