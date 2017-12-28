<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionRole extends Model
{
	protected $table = 'position_role';
    protected $fillable = [
    	'id', 
    	'position_id',
        'role_id',
        'author_id',
    ];
}
