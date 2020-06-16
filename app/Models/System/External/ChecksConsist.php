<?php

namespace App\Models\System\External;

use Illuminate\Database\Eloquent\Model;

class ChecksConsist extends Model
{
    protected $table = 'checks_checkconsist';
    protected $connection = 'mysql2';
    protected $dates = [
        'created'
    ];

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
