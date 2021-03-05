<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Cmvable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Room extends BaseModel
{
    use SoftDeletes,
        Cmvable;
    //    use Cachable;

    const ALIAS = 'rooms';
    const DEPENDENCE = false;

    protected $fillable = [
        'category_id',
        'article_id',
        'location_id',
        'area',
        'price_unit_id',
        'price_unit_category_id',

        'display',
        'system',
        'moderation'
    ];

    public function category()
    {
        return $this->belongsTo(RoomsCategory::class);
    }

    // Локация
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Склады
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'entity_metric_value')
            ->withPivot('value');
    }


}
