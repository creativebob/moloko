<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
        protected $fillable = [
       	'right_name', 
		'right_action', 
		'entity_type', 
		'entity_id', 
    ];
}
