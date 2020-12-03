<?php

namespace App\Models\System\Stocks;

use App\Container;

class ContainersStock extends CmvStock
{
	const ALIAS = 'containers_stocks';
    const DEPENDENCE = true;

    public function cmv()
    {
        return $this->belongsTo(Container::class);
    }
}
