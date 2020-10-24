<?php

namespace App\Observers\System;

use App\MailingList;

class MailingListObserver extends BaseObserver
{
    /**
     * Handle the MailingList "creating" event.
     *
     * @param mailingList $mailingList
     */
    public function creating(MailingList $mailingList)
    {
        $this->store($mailingList);
    }

    /**
     * Handle the MailingList "updating" event.
     *
     * @param mailingList $mailingList
     */
    public function updating(MailingList $mailingList)
    {
        $this->update($mailingList);
    }

    /**
     * Handle the Mailing "deleting" event.
     *
     * @param mailingList $mailingList
     */
    public function deleting(MailingList $mailingList)
    {
        $this->destroy($mailingList);
    }
}
