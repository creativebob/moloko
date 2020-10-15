<?php

namespace App\Models\System\Documents;

use App\Models\System\BaseModel;
use App\Stock;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Consignment extends BaseModel
{

    use SoftDeletes;
//    use Cachable;

    protected $dates = [
        'date',
        'receipted_at',
        'deleted_at',
    ];

    protected $fillable = [
        'supplier_id',
        'stock_id',

        'name',
        'description',

        'date',
        'number',

        'amount',

	    'receipted_at',

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
        return $this->hasMany(ConsignmentsItem::class);
    }
}
