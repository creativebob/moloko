<?php

namespace App\Observers\System\Stocks;

use App\Models\System\Stocks\ToolsStock;

class ToolsStockObserver extends CmvStockObserver
{

    public function creating(ToolsStock $toolsStock)
    {
        $this->store($toolsStock);
    }

    public function updating(ToolsStock $toolsStock)
    {
        $this->update($toolsStock);
        $this->setFree($toolsStock);
    }

    public function deleting(ToolsStock $toolsStock)
    {
        $this->destroy($toolsStock);
    }
}
