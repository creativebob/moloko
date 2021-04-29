<?php

namespace App\Models\System\Flows;

use App\Department;
use App\Manufacturer;
use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessFlow extends BaseModel
{
    use SoftDeletes;
//    use Cachable;

    protected $dates = [
        'start_at',
        'finish_at',
        'started_at',
        'finished_at',

        'deleted_at',
    ];

    protected $fillable = [
        'filial_id',
        'process_id',

        'manufacturer_id',

        'start_at',
        'finish_at',
        'started_at',
        'finished_at',

        'capacity_min',
        'capacity_max',
    ];

    public function filial()
    {
        return $this->belongsTo(Department::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

//    public function reserves()
//    {
//        return $this->morphMany(Reserve::class, 'storage');
//    }

    public function scopeFilter($query)
    {
        $filters = $this->getFilters($this::ALIAS);

        if (isset($filters['manufacturers'])) {
            $query->whereIn('manufacturer_id', $filters['manufacturers']);
        }

        return $query;
    }

}
