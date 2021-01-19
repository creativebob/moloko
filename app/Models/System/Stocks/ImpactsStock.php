<?php

namespace App\Models\System\Stocks;

use App\Impact;

class ImpactsStock extends CmvStock
{
    const ALIAS = 'impacts_stocks';
    const DEPENDENCE = true;

    public function cmv()
    {
        return $this->belongsTo(Impact::class);
    }
}
