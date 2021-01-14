<?php

namespace App\Observers\System;

use App\Impact;

class ImpactObserver extends BaseObserver
{
    /**
     * Handle the impact "creating" event.
     *
     * @param Impact $impact
     */
    public function creating(Impact $impact)
    {
        $this->store($impact);
    }

    /**
     * Handle the impact "updating" event.
     *
     * @param Impact $impact
     */
    public function updating(Impact $impact)
    {
        $this->update($impact);
    }

    /**
     * Handle the impact "deleting" event.
     *
     * @param Impact $impact
     */
    public function deleting(Impact $impact)
    {
        $this->destroy($impact);
    }
}
