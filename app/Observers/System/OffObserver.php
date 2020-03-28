<?php

namespace App\Observers\System;

use App\Off;

use App\Observers\System\Traits\Commonable;

class OffObserver
{

    use Commonable;

    public function creating(Off $off)
    {
        $this->store($off);
    }

    public function updating(Off $off)
    {
        $this->update($off);
    }

    public function deleting(Off $off)
    {
        $this->destroy($off);
    }
}
