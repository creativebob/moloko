<?php

namespace App\Models\System\Stocks;

use App\Raw;

class RawsStock extends CmvStock
{
    public function cmv()
    {
        return $this->belongsTo(Raw::class);
    }
}
