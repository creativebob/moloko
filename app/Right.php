<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Right extends Model
{
		use SoftDeletes;
		
  		protected $dates = ['deleted_at'];
        protected $fillable = [
       	'right_name', 
		'right_action', 
		'category_right_id', 
    ];
}
