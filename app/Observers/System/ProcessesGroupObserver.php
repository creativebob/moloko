<?php

namespace App\Observers\System;

use App\ProcessesGroup;

class ProcessesGroupObserver extends BaseObserver
{
    /**
     * Handle the processesGroup "creating" event.
     *
     * @param ProcessesGroup $processesGroup
     */
    public function creating(ProcessesGroup $processesGroup)
    {
        $this->store($processesGroup);
    }

    /**
     * Handle the processesGroup "updating" event.
     *
     * @param ProcessesGroup $processesGroup
     */
    public function updating(ProcessesGroup $processesGroup)
    {
        $this->update($processesGroup);
    }

    /**
     * Handle the processesGroup "deleting" event.
     *
     * @param ProcessesGroup $processesGroup
     */
    public function deleting(ProcessesGroup $processesGroup)
    {
        $this->destroy($processesGroup);
    }
}
