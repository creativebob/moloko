<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldRawsCategory extends Model
{
    protected $table = 'raws_categories';
    protected $connection = 'mysql2';
}
