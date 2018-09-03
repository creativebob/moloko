<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldCity extends Model
{
    protected $table = 'cities';
    protected $connection = 'mysql2';
}
