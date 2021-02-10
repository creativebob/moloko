<?php

namespace App;

use App\Models\System\BaseModel;

class Shift extends BaseModel
{
    protected $dates = [
        'date',

        'opened_at',
        'closed_at',
    ];

    protected $fillable = [
        'filial_id',
        'outlet_id',

        'date',

        'opened_at',
        'closed_at',

        'balance_open',
        'balance_close',

        'is_opened',
        'is_reopened',

        'display',
        'system',
        'moderation'
    ];

    public function filial()
    {
        return $this->belongsTo(Department::class, 'filial_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
