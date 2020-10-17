<?php

namespace App\Models\System\Stocks;

use App\Container;

class ContainersStock extends CmvStock
{
    public function cmv()
    {
        return $this->belongsTo(Container::class);
    }
}
