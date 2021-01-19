<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Stocks\ImpactsStock;
use App\Models\System\Traits\Cmvable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Impact extends BaseModel
{
    use SoftDeletes,
        Cmvable;

    protected $fillable = [
        'category_id',
        'article_id',

        'unit_for_composition_id',
        'price_unit_id',
        'price_unit_category_id',

        'portion_status',
        'portion_name',
        'portion_abbreviation',
        'unit_portion_id',
        'portion_count',

        'archive',
        'serial',

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(ImpactsCategory::class);
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(ImpactsStock::class, 'cmv_id');
    }

    public function getNameAttribute()
    {
        $value = $this->article->name;
        return $value;
    }
}
