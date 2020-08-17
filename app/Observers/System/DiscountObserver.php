<?php

namespace App\Observers\System;

use App\Discount;
use App\Observers\System\Traits\Commonable;
use App\Observers\System\Traits\Timestampable;

class DiscountObserver
{

    use Commonable;
    use Timestampable;

    public function creating(Discount $discount)
    {
        $this->store($discount);
        $this->setBeginedAt($discount);
        $this->setEndedAt($discount);
    }

    public function updating(Discount $discount)
    {
        $this->update($discount);

        if (! $discount->archive) {
            $this->setEndedAt($discount);
        }

    }

    public function deleting(Discount $discount)
    {
        $this->destroy($discount);
    }

    public function setBeginedAt($discount)
    {
        $beginedAt = $this->getTimestamp('begin', true);
        $discount->begined_at = $beginedAt;
    }

    public function setEndedAt($discount)
    {
        $endedAt = $this->getTimestamp('end');
        $discount->ended_at = $endedAt;
    }
}
