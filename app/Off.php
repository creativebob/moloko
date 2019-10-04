<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Off extends Model
{
    protected $fillable = [
        'stock_id',
        'document_id',
        'document_type',
        'documents_item_id',
        'documents_item_type',
        'cmv_id',
        'cmv_type',
        'count',
        'cost',
    ];
	
	// Тмц
	public function cmv()
	{
		return $this->morphTo();
	}
}
