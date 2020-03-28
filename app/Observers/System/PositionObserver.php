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
        $position->sector_id = \Auth::user()->company->sector_id;

        // TODO - 26.03.20 - Нужно блочить на уровне бога, т.к. шаблоннная должность создается без компании
//        if($position->company_id == null) {
//            abort(403, 'Необходимо авторизоваться под компанией');
//        };
    }

    public function updating(Position $position)
    {
        $this->update($position);
    }
}
