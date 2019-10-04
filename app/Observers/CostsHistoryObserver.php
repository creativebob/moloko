<?php

namespace App\Observers;

use App\CostsHistory;

use App\Observers\Traits\Commonable;

class CostsHistoryObserver
{

    use Commonable;

    public function creating(CostsHistory $costs_history)
    {
        $this->store($costs_history);
        $this->setBeginDate($costs_history);
    }

    public function updating(CostsHistory $costs_history)
    {
        $this->update($costs_history);
    }

    public function deleting(CostsHistory $costs_history)
    {
        $this->destroy($costs_history);
    }

    protected function setBeginDate($costs_history)
    {
        $costs_history->begin_date = now();
    }

}
