<?php

namespace App\Observers\System;

use App\Promotion;

use App\Observers\System\Traits\Commonable;

class PromotionObserver
{
    use Commonable;

    public function creating(Promotion $promotion)
    {
        $this->store($promotion);
        $promotion->filial_id = \Auth::user()->filial_id;
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
