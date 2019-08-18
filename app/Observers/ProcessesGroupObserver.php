<?php

namespace App\Observers;

use App\ProcessesGroup;
use App\Observers\Traits\CommonTrait;

class ProcessesGroupObserver
{
    use CommonTrait;

    public function creating(ProcessesGroup $processes_group)
    {
        $this->store($processes_group);
    }

    public function updating(ProcessesGroup $processes_group)
    {
        $this->update($processes_group);
    }

    public function deleting(ProcessesGroup $processes_group)
    {
        $this->destroy($processes_group);
    }
}
