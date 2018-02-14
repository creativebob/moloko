<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RightRole extends Model
{
	protected $table = 'right_role';
    protected $fillable = [
    	'right_id', 
		'role_id', 
    ];
}
