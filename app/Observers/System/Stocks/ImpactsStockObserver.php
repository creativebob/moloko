<?php

namespace App\Observers\System\Stocks;

use App\Models\System\Stocks\ImpactsStock;

class ImpactsStockObserver extends CmvStockObserver
{

    public function creating(ImpactsStock $impactsStock)
    {
        $this->store($impactsStock);
    }

    public function updating(ImpactsStock $impactsStock)
    {
        $this->update($impactsStock);
        $this->setFree($impactsStock);
    }

    public function deleting(ImpactsStock $impactsStock)
    {
        $this->destroy($impactsStock);
    }
}
