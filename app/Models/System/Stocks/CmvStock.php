<?php

namespace App\Models\System\Stocks;

use App\Department;
use App\Manufacturer;
use App\Models\System\BaseModel;
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
}
