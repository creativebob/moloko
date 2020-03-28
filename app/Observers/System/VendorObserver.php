<?php

namespace App\Observers\System;

use App\Vendor;
use App\Observers\System\Traits\Commonable;

class VendorObserver
{

    use Commonable;

    public function creating(Vendor $vendor)
    {
        $this->store($vendor);
    }

    public function updating(Vendor $vendor)
    {
        $this->update($vendor);
    }

    public function deleting(Vendor $vendor)
    {
        $this->destroy($vendor);
    }
}
