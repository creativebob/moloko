<?php

namespace App\Observers\System;

use App\ReservesHistory;

use App\Observers\System\Traits\Commonable;

class ReservesHistoryObserver
{

    use Commonable;

    public function creating(ReservesHistory $reserves_history)
    {
        $this->store($reserves_history);
    }

    public function updating(ReservesHistory $reserves_history)
    {
        $this->update($reserves_history);
    }


}
