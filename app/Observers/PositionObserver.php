<?php

namespace App\Observers;

use App\Position;

use App\Observers\Traits\Commonable;

class PositionObserver
{

    use Commonable;

    public function creating(Position $position)
    {
        $this->store($position);
        $position->sector_id = \Auth::user()->company->sector_id;

        if($position->company_id == null) {
            abort(403, 'Необходимо авторизоваться под компанией');
        };
    }

    public function updating(Position $position)
    {
        $this->update($position);
    }

    public function deleting(Position $position)
    {
        $this->destroy($position);
    }
}
