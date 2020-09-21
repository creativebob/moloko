<?php

namespace App\Observers\System;

use App\Manufacturer;
use App\Observers\System\Traits\Commonable;

class ManufacturerObserver
{

    use Commonable;

    /**
     * Handle the manufacturer "creating" event.
     *
     * @param Manufacturer $manufacturer
     */
    public function creating(Manufacturer $manufacturer)
    {
        $this->store($manufacturer);
        $manufacturer->display = false;
    }

    /**
     * Handle the manufacturer "updating" event.
     *
     * @param Manufacturer $manufacturer
     */
    public function updating(Manufacturer $manufacturer)
    {
        $this->update($manufacturer);
    }
}
