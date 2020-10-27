<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Archivable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Subscriber extends BaseModel
{
    use Archivable,
        SoftDeletes,
        Cachable;

    protected $dates = [
        'denied_at',
        'archived_at',
    ];

    protected $fillable = [
        'subscriberable_id',
        'subscriberable_type',
        'client_id',
        'site_id',

        'denied_at',
        'is_active',

        'name',
        'email',

        'archived_at',

        'display',
        'system',
        'moderation'
    ];

    public function subscriberable()
    {
        return $this->morphTo();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function dispatches()
    {
        return $this->morphMany(Dispatch::class, 'dispatchable');
    }

    /**
     * Фильтр
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query)
    {
        if (!is_null(request('is_active'))) {
            $query->where('is_active', request('is_active'));
        }

//        if (request('dispatches_count_min')) {
//            $query->where('orders_count', '>=', request('dispatches_count_min'));
//        }
//
//        if (request('dispatches_count_max')) {
//            $query->where('orders_count', '<=', request('dispatches_count_max'));
//        }

        if (request('created_at_min')) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d.m.Y', request()->created_at_min));
        }

        if (request('created_at_max')) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d.m.Y', request()->created_at_max));
        }

        return $query;
    }

}
