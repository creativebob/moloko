<?php

namespace App\Models\System\Stocks;

use App\Raw;

class RawsStock extends CmvStock
{
	const ALIAS = 'raws_stocks';
    const DEPENDENCE = true;

    public function cmv()
    {
        return $this->belongsTo(Raw::class);
    }
}
