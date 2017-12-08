<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{


		public function setMydateAttribute($value){
		    $date_parts = explode('.', $value);
		    $this->attributes['mydate'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
		}

        protected $fillable = [
       	'page_name', 
		'site_id ', 
		'page_title', 
		'page_description', 
		'page_alias', 
    ];
}
