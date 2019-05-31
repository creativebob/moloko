<?php

namespace App\Observers;

use App\Workflow;

use App\Observers\Traits\CommonTrait;

class WorkflowObserver
{

    use CommonTrait;

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
