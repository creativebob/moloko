<?php

namespace App\Observers\System\Flows;

use App\Models\System\Flows\ServicesFlow as Flow;

class ServicesFlowObserver extends ProcessFlowObserver
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
}
