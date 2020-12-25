<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Archivable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workplace extends BaseModel
{
    use Archivable,
        SoftDeletes,
        Cachable;

    protected $dates = [
        'archived_at',
    ];

    protected $fillable = [
        'name',
        'description',

        'ip',

        'filial_id',
        'outlet_id',

        'display',
        'system',
        'moderation'
    ];

    public function filial()
    {
        return $this->belongsTo(Department::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staffer::class, 'workplace_staffer');
    }
    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'workplace_tool');
    }
}
