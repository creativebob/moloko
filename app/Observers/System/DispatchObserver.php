<?php

namespace App\Observers\System;

use App\Dispatch;

use App\Observers\System\Traits\Commonable;

class DispatchObserver
{

    use Commonable;

    public function creating(Dispatch $dispatch)
    {
        $this->store($dispatch);
    }

    public function updating(Dispatch $dispatch)
    {
        $this->update($dispatch);
    }

    public function deleting(Dispatch $dispatch)
    {
        $this->destroy($dispatch);
    }
}
