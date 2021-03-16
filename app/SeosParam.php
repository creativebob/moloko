<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeosParam extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'seo_id',

        'param',
        'value',
    ];
}
