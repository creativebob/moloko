<?php

namespace App\Observers\System;

use App\Outcome;
use App\Observers\System\Traits\Commonable;

class OutcomeObserver
{

    use Commonable;

    public function creating(Outcome $outcome)
    {
        $this->store($outcome);
    }

    public function updating(Outcome $outcome)
    {
        $this->update($outcome);
    }

    public function deleting(Outcome $outcome)
    {
        $this->destroy($outcome);
    }
}
