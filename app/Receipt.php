<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'stock_id',

        'document_id',
        'document_type',

        'documents_item_id',
        'documents_item_type',

        'cmv_id',
        'cmv_type',

        'storage_id',
        'storage_type',

        'count',
        'cost',
	    'amount',
    ];

    public function document()
    {
        return $this->morphTo();
    }

    public function documents_item()
    {
        return $this->morphTo();
    }

    public function cmv()
    {
        return $this->morphTo();
    }

    public function storage()
    {
        return $this->morphTo();
    }
}
