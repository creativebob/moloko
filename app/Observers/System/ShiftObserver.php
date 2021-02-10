<?php

namespace App\Observers\System;

use App\Shift;

class ShiftObserver extends BaseObserver
{
    /**
     * Handle the shift "creating" event.
     *
     * @param Shift $shift
     */
    public function creating(Shift $shift)
    {
        $this->store($shift);
    }

    /**
     * Handle the shift "updating" event.
     *
     * @param Shift $shift
     */
    public function updating(Shift $shift)
    {
        $this->update($shift);
    }

}
