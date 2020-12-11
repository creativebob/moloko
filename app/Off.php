<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Off extends BaseModel
{
    protected $fillable = [
        'stock_id',

        'document_id',
        'document_type',

        'documents_item_id',
        'documents_item_type',

        'cmv_id',
        'cmv_type',

        'storage_id',
        'storage_type',

        'count',

        'weight_unit',
        'volume_unit',

        'cost_unit',
        'total',
    ];

    public function getCostTotalAttribute()
    {
        return $this->cost_unit * $this->count;
    }

    public function getWeightTotalAttribute()
    {
        return $this->weight_unit * $this->count;
    }

    public function getVolumeTotalAttribute()
    {
        return $this->volume_unit * $this->count;
    }

    public function document()
    {
        return $this->morphTo();
    }

    public function documents_item()
    {
        return $this->morphTo();
    }

    public function cmv()
    {
        return $this->morphTo();
    }

    public function storage()
    {
        return $this->morphTo();
    }
}
