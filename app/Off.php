<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Off extends Model
{
    protected $fillable = [
        'document_id',
        'document_type',
        'cmv_id',
        'cmv_type',
        'count',
        'average',
    ];
	
	// Тмц
	public function cmv()
	{
		return $this->morphTo();
	}
}
