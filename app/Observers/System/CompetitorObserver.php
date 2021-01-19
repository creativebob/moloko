<?php

namespace App\Observers\System;

use App\Competitor;

class CompetitorObserver extends BaseObserver
{
    /**
     * Handle the competitor "creating" event.
     *
     * @param Competitor $competitor
     */
    public function creating(Competitor $competitor)
    {
        $this->store($competitor);
        $competitor->display = false;
    }

    /**
     * Handle the competitor "updating" event.
     *
     * @param Competitor $competitor
     */
    public function updating(Competitor $competitor)
    {
        $this->update($competitor);
    }
}
