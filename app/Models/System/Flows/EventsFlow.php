<?php

namespace App\Models\System\Flows;

use App\Event;

class EventsFlow extends ProcessFlow
{
    const ALIAS = 'events_flows';
    const DEPENDENCE = true;

    public function process()
    {
        return $this->belongsTo(Event::class);
    }
}
