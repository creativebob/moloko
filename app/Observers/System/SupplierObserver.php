<?php

namespace App\Observers\System;

use App\Supplier;
use App\Observers\System\Traits\Commonable;

class SupplierObserver
{

    use Commonable;

    /**
     * Handle the supplier "creating" event.
     *
     * @param Supplier $supplier
     */
    public function creating(Supplier $supplier)
    {
        $this->store($supplier);
        $supplier->display = false;
    }

    /**
     * Handle the supplier "updating" event.
     *
     * @param Supplier $supplier
     */
    public function updating(Supplier $supplier)
    {
        $this->update($supplier);
    }
}
