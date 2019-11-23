<?php

namespace App\Observers;

use App\Promotion;

use App\Observers\Traits\Commonable;

class PromotionObserver
{
    use Commonable;

    public function creating(Promotion $promotion)
    {
        $this->store($promotion);
    }

    public function updating(Promotion $promotion)
    {
        $this->update($promotion);
    }

    public function deleting(Promotion $promotion)
    {
        $this->destroy($promotion);
    }
}
