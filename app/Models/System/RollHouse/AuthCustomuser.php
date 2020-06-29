<?php

namespace App\Models\System\RollHouse;

use Illuminate\Database\Eloquent\Model;

class AuthCustomuser extends Model
{
    public $timestamps = false;
    protected $table = 'auth_customuser';
    protected $connection = 'mysql2';
    protected $dates = [
        'created'
    ];

    public function user()
    {
        return $this->belongsTo(AuthUser::class, 'user_ptr_id');
    }
}
