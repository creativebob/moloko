<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldLocation extends Model
{
    protected $table = 'locations';
    protected $connection = 'mysql2';
}
