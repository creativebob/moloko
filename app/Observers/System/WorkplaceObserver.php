<?php

namespace App\Observers\System;

use App\Workplace;

class WorkplaceObserver extends BaseObserver
{
    /**
     * Handle the workplace "creating" event.
     *
     * @param Workplace $workplace
     */
    public function creating(Workplace $workplace)
    {
        $this->store($workplace);
    }

    /**
     * Handle the workplace "updating" event.
     *
     * @param Workplace $workplace
     */
    public function updating(Workplace $workplace)
    {
        $this->update($workplace);
    }
}
