<?php

namespace App\Observers\System;

use App\Page;
use App\Position;
use App\Observers\System\Traits\Commonable;

class PositionObserver
{

    use Commonable;

    public function creating(Position $position)
    {
        $this->store($position);
        $position->sector_id = auth()->user()->company->sector_id ?? null;

        $page_id = Page::where('alias', 'dashboard')->value('id');
        $position->page_id = $page_id;
    }

    public function updating(Position $position)
    {
        $this->update($position);
    }
}
