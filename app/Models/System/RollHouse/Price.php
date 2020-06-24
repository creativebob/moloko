<?php

namespace App\Models\System\RollHouse;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'prices_prices';
    protected $connection = 'mysql2';
    protected $dates = [
        'created'
    ];
}
