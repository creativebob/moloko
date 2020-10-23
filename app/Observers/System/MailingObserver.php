<?php

namespace App\Observers\System;

use App\Mailing;

class MailingObserver extends BaseObserver
{
    /**
     * Handle the Mailing "creating" event.
     *
     * @param mailing $mailing
     */
    public function creating(Mailing $mailing)
    {
        $this->store($mailing);
    }

    /**
     * Handle the Mailing "updating" event.
     *
     * @param mailing $mailing
     */
    public function updating(Mailing $mailing)
    {
        $this->update($mailing);
    }

    /**
     * Handle the Mailing "deleting" event.
     *
     * @param mailing $mailing
     */
    public function deleting(Mailing $mailing)
    {
        $this->destroy($mailing);
    }
}
