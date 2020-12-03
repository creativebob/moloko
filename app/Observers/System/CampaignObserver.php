<?php

namespace App\Observers\System;

use App\Campaign;
use App\Observers\System\Traits\Commonable;

class CampaignObserver
{

    use Commonable;

    public function creating(Campaign $campaign)
    {
        $this->store($campaign);
    }

    public function updating(Campaign $campaign)
    {
        $this->update($campaign);
    }

    public function deleting(Campaign $campaign)
    {
        $this->destroy($campaign);
    }

}
