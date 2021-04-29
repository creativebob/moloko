<?php

namespace App\Observers\System;

use App\Event;

class EventObserver extends BaseObserver
{
    /**
     * Handle the event "creating" event.
     *
     * @param Event $event
     */
    public function creating(Event $event)
    {
        $this->store($event);
    }

    /**
     * Handle the event "updating" event.
     *
     * @param Event $event
     */
    public function updating(Event $event)
    {
        $this->update($event);
    }

    /**
     * Handle the event "deleting" event.
     *
     * @param Event $event
     */
    public function deleting(Event $event)
    {
        $this->destroy($event);
    }
}
