<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Locationable;
use App\Models\System\Traits\Phonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends BaseModel
{
    use SoftDeletes,
        Cachable,
        Locationable,
        Phonable;

    protected $fillable = [
        'name',
        'description',

        'location_id',
        'stock_id',
        'template_id',

        'filial_id',

        'display',
        'system',
        'moderation'
    ];

    public function filial()
    {
        return $this->belongsTo(Department::class, 'filial_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function taxation_types()
    {
        return $this->belongsToMany(TaxationType::class, 'outlet_taxation_type');
    }

    public function catalogs_goods()
    {
        return $this->belongsToMany(CatalogsGoods::class, 'outlet_catalog_goods', 'outlet_id', 'catalog_goods_id');
    }

    public function staff()
    {
        return $this->belongsToMany(Staffer::class, 'outlet_staffer');
    }

    public function settings()
    {
        return $this->belongsToMany(OutletsSetting::class, 'outlet_setting', 'outlet_id', 'setting_id');
    }
}
