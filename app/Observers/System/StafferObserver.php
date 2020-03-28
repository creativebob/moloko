<?php

namespace App\Observers\System;

use App\Staffer;

use App\Observers\System\Traits\Commonable;

class StafferObserver
{

    use Commonable;

    public function creating(Staffer $staffer)
    {
        $this->store($staffer);
    }

//    public function updating(Staffer $staffer)
//    {
//        $this->update($staffer);
//    }
}
