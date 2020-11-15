<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Payment extends BaseModel
{
    use SoftDeletes;
//    use Cachable;

    protected $with = [
        'method',
        'currency'
    ];

    protected $dates = [
        'registered_at',
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
        'currency_id',

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

    public function type()
    {
        return $this->belongsTo(PaymentsType::class, 'payments_type_id');
    }

    public function method()
    {
        return $this->belongsTo(PaymentsMethod::class, 'payments_method_id');
    }

    public function contract()
    {
        return $this->morphTo();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
