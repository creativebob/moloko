<?php

namespace App\Models\System\External;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    protected $table = 'checks_checks';
    protected $connection = 'mysql2';
    protected $dates = [
        'created'
    ];

    public function consists()
    {
        return $this->hasMany(ChecksConsist::class, 'check_id');
    }
}
