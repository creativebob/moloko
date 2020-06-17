<?php

namespace App\Models\System\External;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $connection = 'mysql2';

}
