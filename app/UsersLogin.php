<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersLogin extends Model
{

    public $timestamps = false;

    protected $dates = [
        'logined_at',
    ];

    protected $fillable = [
        'user_id',
        'logined_at',
        'ip',
    ];
}
