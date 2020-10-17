<?php
    
    namespace App\Observers\System\Documents;

use App\Models\System\Documents\Estimate;
use App\Observers\System\BaseObserver;

class EstimateObserver extends BaseObserver
{
    /**
     * Handle the estimate "creating" event.
     *
     * @param Estimate $estimate
     */
    public function creating(Estimate $estimate)
    {
        $this->store($estimate);
        $estimate->currency_id = 1;
//        $estimate->date = now()->format('d.m.Y');
    }
    
    /**
     * Handle the estimate "updating" event.
     *
     * @param Estimate $estimate
     */
    public function updating(Estimate $estimate)
    {
        $this->update($estimate);
    }
    
    /**
     * Handle the estimate "deleting" event.
     *
     * @param Estimate $estimate
     */
    public function deleting(Estimate $estimate)
    {
        $this->destroy($estimate);
    }
}
