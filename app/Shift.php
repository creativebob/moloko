<?php

namespace App;

use App\Models\System\BaseModel;
use Carbon\Carbon;

class Shift extends BaseModel
{

    const ALIAS = 'shifts';
    const DEPENDENCE = true;

    protected $dates = [
        'date',

        'opened_at',
        'closed_at',

        'need_closed_at',
    ];

    protected $fillable = [
        'filial_id',
        'outlet_id',

        'date',

        'opened_at',
        'closed_at',

        'need_closed_at',

        'balance_open',
        'balance_close',

        'cash',
        'electronically',

        'is_opened',
        'is_reopened',

        'display',
        'system',
        'moderation'
    ];

//    public function setDateAttribute($value)
//    {
//        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
//    }

    public function filial()
    {
        return $this->belongsTo(Department::class, 'filial_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
