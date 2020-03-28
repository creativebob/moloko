<?php

namespace App\Observers\System;

use App\ContainersStock;
use App\Observers\System\Traits\Commonable;

class ContainersStockObserver
{

    use Commonable;

    public function creating(ContainersStock $containers_stock)
    {
        $this->store($containers_stock);
    }

    public function updating(ContainersStock $containers_stock)
    {
        $this->update($containers_stock);
    }

    public function deleting(ContainersStock $containers_stock)
    {
        $this->destroy($containers_stock);
    }

}
