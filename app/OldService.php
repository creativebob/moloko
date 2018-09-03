<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldService extends Model
{
    protected $table = 'services';
    protected $connection = 'mysql2';
}
