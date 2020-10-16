<?php

namespace App\Observers\System\Stocks;

use App\Models\System\Stocks\RawsStock;

class RawsStockObserver extends CmvStockObserver
{

    public function creating(RawsStock $rawsStock)
    {
        $this->store($rawsStock);
    }

    public function updating(RawsStock $rawsStock)
    {
        $this->update($rawsStock);
        $this->setFree($rawsStock);
    }

    public function deleting(RawsStock $rawsStock)
    {
        $this->destroy($rawsStock);
    }
}
