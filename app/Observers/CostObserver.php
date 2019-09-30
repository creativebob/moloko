<?php

namespace App\Observers;

use App\Cost;
use App\Observers\Traits\Commonable;

class CostObserver
{

    use Commonable;

    public function creating(Cost $cost)
    {
        $this->store($cost);
    }

    public function updating(Cost $cost)
    {
        $this->update($cost);
    }

    public function deleting(Cost $cost)
    {
        $this->destroy($cost);
    }

}
