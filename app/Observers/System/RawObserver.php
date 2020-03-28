<?php

namespace App\Observers\System;

use App\Raw;
use App\Observers\System\Traits\Commonable;

class RawObserver
{

    use Commonable;

    public function creating(Raw $raw)
    {
        $this->store($raw);
    }

    public function updating(Raw $raw)
    {
        $this->update($raw);
    }

    public function deleting(Raw $raw)
    {
        $this->destroy($raw);
    }
}
