<?php

namespace App\Observers;

use App\Supplier;
use App\Observers\Traits\Commonable;

class SupplierObserver
{

    use Commonable;

    public function creating(Supplier $supplier)
    {
        $this->store($supplier);
    }

    public function updating(Supplier $supplier)
    {
        $this->update($supplier);
    }

    public function deleting(Supplier $supplier)
    {
        $this->destroy($supplier);
    }
}
