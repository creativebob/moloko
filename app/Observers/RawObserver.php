<?php

namespace App\Observers;

use App\Raw;

use App\Observers\Traits\CommonTrait;

class RawObserver
{

    use CommonTrait;

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
