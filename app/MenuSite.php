<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuSite extends Model
{
    protected $table = 'menu_site';
    protected $fillable = [
    	'menu_id', 
		'site_id', 
    ];

    
}
