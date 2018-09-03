<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldTask extends Model
{
    protected $table = 'tasks';
    protected $connection = 'mysql2';
}
