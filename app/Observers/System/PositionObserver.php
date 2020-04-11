<?php

namespace App\Observers\System;

use App\Position;
use App\Observers\System\Traits\Commonable;

class PositionObserver
{

    use Commonable;

    public function creating(Position $position)
    {
        $this->store($position);
        $position->sector_id = auth()->user()->company->sector_id;
    }

    public function updating(Position $position)
    {
        $this->update($position);
    }
}
