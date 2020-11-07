<?php

namespace App\Observers\System;

use App\Outlet;

class OutletObserver extends BaseObserver
{
    /**
     * Handle the Outlet "creating" event.
     *
     * @param outlet $outlet
     */
    public function creating(Outlet $outlet)
    {
        $this->store($outlet);
    }

    /**
     * Handle the Outlet "updating" event.
     *
     * @param outlet $outlet
     */
    public function updating(Outlet $outlet)
    {
        $this->update($outlet);
    }

    /**
     * Handle the Outlet "deleting" event.
     *
     * @param outlet $outlet
     */
    public function deleting(Outlet $outlet)
    {
        $this->destroy($outlet);
    }
}
