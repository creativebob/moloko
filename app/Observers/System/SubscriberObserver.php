<?php

namespace App\Observers\System;

use App\Subscriber;

class SubscriberObserver extends BaseObserver
{
    /**
     * Handle the subscriber "creating" event.
     *
     * @param Subscriber $subscriber
     */
    public function creating(Subscriber $subscriber)
    {
        $this->store($subscriber);
    }

    /**
     * Handle the subscriber "updating" event.
     *
     * @param Subscriber $subscriber
     */
    public function updating(Subscriber $subscriber)
    {
        $this->update($subscriber);
    }

    /**
     * Handle the subscriber "deleting" event.
     *
     * @param Subscriber $subscriber
     */
    public function deleting(Subscriber $subscriber)
    {
        $this->destroy($subscriber);
    }
}
