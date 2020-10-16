<?php

namespace App\Observers\System\Stocks;

use App\Models\System\Stocks\ContainersStock;

class ContainersStockObserver extends CmvStockObserver
{

    public function creating(ContainersStock $containersStock)
    {
        $this->store($containersStock);
    }

    public function updating(ContainersStock $containersStock)
    {
        $this->update($containersStock);
        $this->setFree($containersStock);
    }

    public function deleting(ContainersStock $containersStock)
    {
        $this->destroy($containersStock);
    }
}
