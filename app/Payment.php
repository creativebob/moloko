<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Quietlable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Payment extends BaseModel
{
    use SoftDeletes,
        Quietlable;
//    use Cachable;

    protected $dates = [
        'registered_at',
        'canceled_at',
        'deleted_at',
    ];

    protected $fillable = [
        'registered_at',

        'cash',
        'electronically',
        'total',

        'cash_taken',
        'cash_change',

        'type',

        'payments_method_id',
        'payments_sign_id',

        'currency_id',
        'shift_id',

        'canceled_payment_id',
        'canceled_at',

        'contract_id',
        'contract_type',

        'document_id',
        'document_type',

        'display',
        'system',
        'moderation'
    ];

//    public function setDateAttribute($value)
//    {
//        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
//    }

    public function method()
    {
        return $this->belongsTo(PaymentsMethod::class, 'payments_method_id');
    }

    public function sign()
    {
        return $this->belongsTo(PaymentsSign::class, 'payments_sign_id');
    }

//    public function payments_type()
//    {
//        return $this->belongsTo(PaymentsType::class, 'payments_type_id');
//    }

    public function document()
    {
        return $this->morphTo();
    }

    public function contract()
    {
        return $this->morphTo();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
