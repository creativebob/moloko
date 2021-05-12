<?php

namespace App\Models\System\Flows;

use App\Event;
use App\Staffer;

class EventsFlow extends ProcessFlow
{
    const ALIAS = 'events_flows';
    const DEPENDENCE = true;

    public function process()
    {
        return $this->belongsTo(Event::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staffer::class, 'events_flow_staffer');
    }
}
