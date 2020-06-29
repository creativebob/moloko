<?php

namespace App\Models\System\RollHouse;

use Illuminate\Database\Eloquent\Model;

class Employeer extends Model
{
    public $timestamps = false;
    protected $table = 'employers_employers';
    protected $connection = 'mysql2';
    protected $dates = [
        'created'
    ];

    public function user()
    {
        return $this->belongsTo(AuthCustomuser::class, 'user_id', 'user_ptr_id');
    }
}
