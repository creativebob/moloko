<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use Cachable;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'alias',
        'filters',
    ];

//    protected $casts = [
//        'filters' => 'array'
//    ];

    public function getFiltersArrayAttribute()
    {
        return json_decode($this->filters, true);
    }
}
