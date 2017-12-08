<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Access_group extends Model
{

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'id', 
        'access_group_name', 
        'category_right_id', 
    ];
}
