<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class List_item extends Model
{

 	use SoftDeletes;
		
  	protected $dates = ['deleted_at'];
    protected $fillable = [
	    'item_entity', 
	    'booklist_id', 
    ];

    /**
  * Получаем запись (ID списка).
  */
  public function booklist()
  {
    return $this->belongsTo('App\Booklist');
  }

}
