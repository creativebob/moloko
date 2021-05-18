<?php

namespace App\Observers\System\Flows;

use App\Models\System\Flows\EventsFlow as Flow;

class EventsFlowObserver extends ProcessFlowObserver
{

    public function creating(Flow $flow)
    {
        $this->store($flow);
        $this->setManufacturer($flow);
    }

    public function updating(Flow $flow)
    {
        $this->update($flow);
        $this->setManufacturer($flow);
    }

    public function deleting(Flow $flow)
    {
        $this->destroy($flow);
    }
}
