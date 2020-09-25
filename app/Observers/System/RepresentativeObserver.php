<?php

namespace App\Observers\System;

use App\Representative;
use App\Observers\System\Traits\Commonable;

class RepresentativeObserver
{

    use Commonable;

    public function creating(Representative $representative)
    {
        $this->store($representative);
    }

    public function updating(Representative $representative)
    {
        $this->update($representative);
    }

    public function deleting(Representative $representative)
    {
        $this->destroy($representative);
    }
}
