<?php

namespace App\Observers;

use App\Estimate;

use App\Observers\Traits\Commonable;

class EstimateObserver
{

    use Commonable;

    public function creating(Estimate $estimate)
    {
        $this->store($estimate);
        $estimate->date = now()->format('d.m.Y');
	
//	    $estimate->filial_id = \Auth::user()->stafferFilialId;
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
