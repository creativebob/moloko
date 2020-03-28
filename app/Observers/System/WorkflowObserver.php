<?php

namespace App\Observers\System;

use App\Workflow;

use App\Observers\System\Traits\Commonable;

class WorkflowObserver
{

    use Commonable;

    public function creating(Workflow $workflow)
    {
        $this->store($workflow);
    }

    public function updating(Workflow $workflow)
    {
        $this->update($workflow);
    }

    public function deleting(Workflow $workflow)
    {
        $this->destroy($workflow);
    }
}
