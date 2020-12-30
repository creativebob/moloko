<?php

namespace App\Observers\System;

use App\Workflow;

class WorkflowObserver extends BaseObserver
{
    /**
     * Handle the workflow "creating" event.
     *
     * @param Workflow $workflow
     */
    public function creating(Workflow $workflow)
    {
        $this->store($workflow);
    }

    /**
     * Handle the workflow "updating" event.
     *
     * @param Workflow $workflow
     */
    public function updating(Workflow $workflow)
    {
        $this->update($workflow);
    }

    /**
     * Handle the workflow "deleting" event.
     *
     * @param Workflow $workflow
     */
    public function deleting(Workflow $workflow)
    {
        $this->destroy($workflow);
    }
}
