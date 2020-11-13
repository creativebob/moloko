<?php

namespace App\Models\System\Documents;

use App\Models\System\BaseModel;
use App\Receipt;
use App\Stock;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Consignment extends BaseModel
{

    use SoftDeletes;
//    use Cachable;

    const ALIAS = 'consignment';
    const DEPENDENCE = true;

    protected $dates = [
        'date',
        'conducted_at',
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
        return $this->hasMany(ConsignmentsItem::class);
    }

    public function receipts()
    {
        return $this->morphMany(Receipt::class, 'document');
    }



    /**
     * Фильтр
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query)
    {
        $filters = $this->getFilters(Consignment::ALIAS);

        if (isset($filters['suppliers'])) {
                $query->whereIn('supplier_id', $filters['supplier']);
        }

        if (isset($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (isset($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }


//         if (isset($filters['first_order_date_min'])) {
//             $query->whereDate('first_order_date', '>=', Carbon::createFromFormat('d.m.Y', $filters['first_order_date_min']));
//         }
//         if (isset($filters['first_order_date_max'])) {
//             $query->whereDate('first_order_date', '<=', Carbon::createFromFormat('d.m.Y', $filters['first_order_date_max']));
//         }



        return $query;
    }




}
