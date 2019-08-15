<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeftoverOperation extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',

        'display',
        'system',
        'moderation'
    ];
}
