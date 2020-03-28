<?php

namespace App\Observers\System;

use App\RawsStock;
use App\Observers\System\Traits\Commonable;

class RawsStockObserver
{

    use Commonable;

    public function creating(RawsStock $raws_stock)
    {
        $this->store($raws_stock);
    }

    public function updating(RawsStock $raws_stock)
    {
        $this->update($raws_stock);
    }

    public function deleting(RawsStock $raws_stock)
    {
        $this->destroy($raws_stock);
    }

}
