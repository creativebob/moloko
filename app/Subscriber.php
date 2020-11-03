<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Archivable;
use App\Models\System\Traits\Dispatchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Subscriber extends BaseModel
{
    use Archivable,
        SoftDeletes,
        Cachable,
        Dispatchable;

    protected $withCount = [
        'sendedDispatches'
    ];

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
        'is_valid',
        'is_self',

        'name',
        'email',

        'archived_at',

        'display',
        'system',
        'moderation'
    ];

    public function getGetNameAttribute()
    {
        return isset($this->name) ? $this->name: optional($this->subscriberable)->name;
    }

    public function subscriberable()
    {
        return $this->morphTo();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Активность
     *
     * @param $query
     * @param bool $value
     */
    public function scopeActive($query, $value = true)
    {
        $query->where("is_active", $value);
    }

    /**
     * Валидность
     *
     * @param $query
     * @param bool $value
     */
    public function scopeValid($query, $value = true)
    {
        $query->where("is_valid", $value);
    }

    /**
     * Отправка разрешена
     *
     * @param $query
     */
    public function scopeAllow($query)
    {
        $query->whereNull('denied_at');
    }

    /**
     * Отправка запрещена
     *
     * @param $query
     */
    public function scopeDeny($query)
    {
        $query->whereNotNull('denied_at');
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
            $query->active(request('is_active'));
        }

        if (!is_null(request('deny'))) {
            if (request('deny') == 1) {
                $query->allow();
            } else {
                $query->deny();
            }
        }

        if (!is_null(request('client'))) {
            if (request('client') == 1) {
                $query->whereNotNull('client_id');
            } else {
                $query->whereNull('client_id');
            }
        }

        if (!is_null(request('is_valid'))) {
            $query->valid(request('is_valid'));
        }

        if (!is_null(request('dispatches_count_min'))) {
            if (request('dispatches_count_min') > 0) {
                $query->has('sendedDispatches', '>=', request('dispatches_count_min'));
            } else {
                $query->doesnthave('sendedDispatches');
            }
        }

        if (!is_null(request('dispatches_count_max'))) {
            if (request('dispatches_count_max') > 0) {
                $query->has('sendedDispatches', '<=', request('dispatches_count_max'));
            } else {
                $query->doesnthave('sendedDispatches');
            }
        }

        if (request('created_at_min')) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d.m.Y', request()->created_at_min));
        }

        if (request('created_at_max')) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d.m.Y', request()->created_at_max));
        }

        return $query;
    }

}
