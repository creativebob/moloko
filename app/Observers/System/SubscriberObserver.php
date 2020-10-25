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

        $token = $this->getToken(\Str::random(30));
        $subscriber->token = $token;
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

    public function getToken($token) {
        $res = Subscriber::where('token', $token)
            ->first();
        if ($res) {
            $token = \Str::random(30);
            $this->getToken($token);
        } else {
            return $token;
        }
    }
}
