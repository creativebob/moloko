<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Commonable;
use App\Portfolio;

class PortfolioObserver
{

    use Commonable;

    public function creating(Portfolio $portfolio)
    {
        $this->store($portfolio);
    }

    public function updating(Portfolio $portfolio)
    {
        $this->update($portfolio);
    }

    public function deleting(Portfolio $portfolio)
    {
        $this->destroy($portfolio);
    }
}
