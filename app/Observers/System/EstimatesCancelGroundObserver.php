<?php

namespace App\Observers\System;

use App\EstimatesCancelGround;

class EstimatesCancelGroundObserver extends BaseObserver
{
    /**
     * Handle the estimatesCancelGround "creating" event.
     *
     * @param EstimatesCancelGround $estimatesCancelGround
     */
    public function creating(EstimatesCancelGround $estimatesCancelGround)
    {
        $this->store($estimatesCancelGround);
    }

    /**
     * Handle the estimatesCancelGround "updating" event.
     *
     * @param EstimatesCancelGround $estimatesCancelGround
     */
    public function updating(EstimatesCancelGround $estimatesCancelGround)
    {
        $this->update($estimatesCancelGround);
    }

    /**
     * Handle the estimatesCancelGround "deleting" event.
     *
     * @param EstimatesCancelGround $estimatesCancelGround
     */
    public function deleting(EstimatesCancelGround $estimatesCancelGround)
    {
        $this->destroy($estimatesCancelGround);
    }
}
