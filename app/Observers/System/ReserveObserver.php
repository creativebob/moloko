<?php

namespace App\Observers\System;

use App\Reserve;

use App\Observers\System\Traits\Commonable;

class ReserveObserver
{

    use Commonable;

    public function creating(Reserve $reserve)
    {
        $this->store($reserve);
    }

    public function created(Reserve $reserve)
    {
        $reserve->history()->create([
            'count' => $reserve->count,
        ]);
    }

    public function updating(Reserve $reserve)
    {
        $this->update($reserve);
    }

    public function deleting(Reserve $reserve)
    {
        $this->destroy($reserve);
    }

}
