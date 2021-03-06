<?php

namespace App\Observers\System;

use App\Cost;
use App\Observers\System\Traits\Commonable;

class CostObserver
{

    use Commonable;

    public function creating(Cost $cost)
    {
        $this->store($cost);
    }

    public function created(Cost $cost)
    {
        $this->setCostHistory($cost);
    }

    public function updating(Cost $cost)
    {
        $this->update($cost);
        $cost->history()->latest()->update([
        	'end_date' => now()
        ]);
	    $this->setCostHistory($cost);
    }

    public function deleting(Cost $cost)
    {
        $this->destroy($cost);
    }

    private function setCostHistory($cost)
    {
        $cost->history()->create([
            'min' => $cost->min,
            'max' => $cost->max,
            'average' => $cost->average,
        ]);
    }

}
