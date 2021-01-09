<?php

namespace App\Observers\System;

use App\Label;

class LabelObserver extends BaseObserver
{
    /**
     * Handle the label "creating" event.
     *
     * @param Label $label
     */
    public function creating(Label $label)
    {
        $this->store($label);
    }

    /**
     * Handle the label "updating" event.
     *
     * @param Label $label
     */
    public function updating(Label $label)
    {
        $this->update($label);
    }

    /**
     * Handle the label "deleting" event.
     *
     * @param Label $label
     */
    public function deleting(Label $label)
    {
        $this->destroy($label);
    }
}
