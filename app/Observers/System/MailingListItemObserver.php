<?php

namespace App\Observers\System;

use App\MailingListItem;

class MailingListItemObserver extends BaseObserver
{
    /**
     * Handle the MailingListItem "creating" event.
     *
     * @param mailingListItem $mailingListItem
     */
    public function creating(MailingListItem $mailingListItem)
    {
        $this->store($mailingListItem);
    }

    /**
     * Handle the MailingListItem "updating" event.
     *
     * @param mailingListItem $mailingListItem
     */
    public function updating(MailingListItem $mailingListItem)
    {
        $this->update($mailingListItem);
    }

    /**
     * Handle the MailingListItem "deleting" event.
     *
     * @param mailingListItem $mailingListItem
     */
    public function deleting(MailingListItem $mailingListItem)
    {
        $this->destroy($mailingListItem);
    }
}
