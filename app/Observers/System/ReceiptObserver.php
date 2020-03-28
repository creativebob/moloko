<?php

namespace App\Observers\System;

use App\Receipt;

use App\Observers\System\Traits\Commonable;

class ReceiptObserver
{

    use Commonable;

    public function creating(Receipt $receipt)
    {
        $this->store($receipt);
    }

    public function updating(Receipt $receipt)
    {
        $this->update($receipt);
    }

    public function deleting(Receipt $receipt)
    {
        $this->destroy($receipt);
    }
}
