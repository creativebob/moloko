<?php

namespace App\Observers\System;

use App\Estimate;

use App\Observers\System\Traits\Commonable;

class EstimateObserver
{

    use Commonable;

    public function creating(Estimate $estimate)
    {
        $this->store($estimate);
        $estimate->date = now()->format('d.m.Y');
    }

    public function updating(Estimate $estimate)
    {
        $this->update($estimate);
    }

    public function deleting(Estimate $estimate)
    {
        $this->destroy($estimate);
    }
}
