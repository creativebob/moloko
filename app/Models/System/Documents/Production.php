<?php

namespace App\Models\System\Documents;

use App\Models\System\BaseModel;
use App\Off;
use App\Receipt;
use App\Stock;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Production extends BaseModel
{

    use SoftDeletes;
//    use Cachable;

    protected $dates = [
        'date',
        'conducted_at',
        'deleted_at',
    ];

    protected $fillable = [
        'manufacturer_id',
        'stock_id',
        'estimate_id',

        'name',
        'description',

        'date',
        'number',

        'amount',

        'conducted_at',

        'draft',

        'display',
        'system',
        'moderation'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function items()
    {
        return $this->hasMany(ProductionsItem::class);
    }

	public function offs()
	{
		return $this->morphMAny(Off::class, 'document');
	}

    public function receipts()
    {
        return $this->morphMany(Receipt::class, 'document');
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }
}
