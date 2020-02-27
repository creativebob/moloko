<?php

namespace App\Observers;

use App\Vendor;
use App\Observers\Traits\Commonable;

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
