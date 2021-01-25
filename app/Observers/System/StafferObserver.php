<?php

namespace App\Observers\System;

use App\Staffer;

class StafferObserver extends BaseObserver
{

    public function creating(Staffer $staffer)
    {
        $this->store($staffer);
    }

    public function updating(Staffer $staffer)
    {
        $this->update($staffer);
    }
}
