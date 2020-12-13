<?php

namespace App\Observers\System;

use App\AgencyScheme;

class AgencySchemeObserver extends BaseObserver
{
    /**
     * Handle the agencyScheme "creating" event.
     *
     * @param AgencyScheme $agencyScheme
     */
    public function creating(AgencyScheme $agencyScheme)
    {
        $this->store($agencyScheme);
    }

    /**
     * Handle the agencyScheme "deleting" event.
     *
     * @param AgencyScheme $agencyScheme
     */
    public function deleting(AgencyScheme $agencyScheme)
    {
        $this->destroy($agencyScheme);
    }
}
