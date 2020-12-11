<?php

namespace App\Models\System\Stocks;

use App\Department;
use App\Manufacturer;
use App\Models\System\BaseModel;
use App\Off;
use App\Receipt;
use App\Reserve;
use App\Stock;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmvStock extends BaseModel
{
    use SoftDeletes;

    // Включаем кеш
//    use Cachable;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'stock_id',
        'filial_id',
        'cmv_id',

        'manufacturer_id',

        'count',
        'reserve',
        'free',

        'weight',
        'volume',
        'serial',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function filial()
    {
        return $this->belongsTo(Department::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function receipts()
    {
        return $this->morphMany(Receipt::class, 'storage');
    }

    public function offs()
    {
        return $this->morphMany(Off::class, 'storage');
    }

    public function reserves()
    {
        return $this->morphMany(Reserve::class, 'storage');
    }

    // Получаем себестоимость остатков по позиции на складе
    public function getStockCostAttribute()
    {
        return $this->cmv->cost_unit * $this->count;
    }

    public function scopeFilter($query)
    {
        $filters = $this->getFilters($this::ALIAS);


        if (isset($filters['count_min'])) {
            $query->where('count', '>=', $filters['count_min']);
        }
        if (isset($filters['count_max'])) {
            $query->where('count', '<=', $filters['count_max']);
        }

        if (isset($filters['free_min'])) {
            $query->where('free', '>=', $filters['free_min']);
        }
        if (isset($filters['free_max'])) {
            $query->where('free', '<=', $filters['free_max']);
        }

        if (isset($filters['reserve_min'])) {
            $query->where('reserve', '>=', $filters['reserve_min']);
        }
        if (isset($filters['reserve_max'])) {
            $query->where('reserve', '<=', $filters['reserve_max']);
        }

        if (isset($filters['weight_min'])) {
            $query->where('weight', '>=', $filters['weight_min']);
        }
        if (isset($filters['weight_max'])) {
            $query->where('weight', '<=', $filters['weight_max']);
        }

        if (isset($filters['volume_min'])) {
            $query->where('volume', '>=', $filters['volume_min']);
        }
        if (isset($filters['volume_max'])) {
            $query->where('volume', '<=', $filters['volume_max']);
        }

        if (isset($filters['manufacturers'])) {
            $query->whereIn('manufacturer_id', $filters['manufacturers']);
        }

        return $query;
    }

}
