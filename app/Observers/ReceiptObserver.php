<?php

namespace App\Observers;

use App\Receipt;

use App\Observers\Traits\Commonable;

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
