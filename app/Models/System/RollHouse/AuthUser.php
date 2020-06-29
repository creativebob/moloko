<?php

namespace App\Models\System\RollHouse;

use Illuminate\Database\Eloquent\Model;

class AuthUser extends Model
{
    public $timestamps = false;
    protected $table = 'auth_user';
    protected $connection = 'mysql2';
    protected $dates = [
        'created'
    ];

    public function user()
    {
        return $this->belongsTo(Client::class);
    }
}
