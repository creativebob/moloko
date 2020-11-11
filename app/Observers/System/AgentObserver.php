<?php

namespace App\Observers\System;

use App\Agent;
use App\Observers\System\Traits\Commonable;

class AgentObserver
{

    use Commonable;

    /**
     * Handle the agent "creating" event.
     *
     * @param Agent $agent
     */
    public function creating(Agent $agent)
    {
        $this->store($agent);
        $agent->display = false;
    }

    /**
     * Handle the agent "updating" event.
     *
     * @param Agent $agent
     */
    public function updating(Agent $agent)
    {
        $this->update($agent);
    }
}
