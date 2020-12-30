<?php

namespace App\Observers\System\Documents;

use App\Models\System\Documents\EstimatesServicesItem;
use App\Observers\System\BaseObserver;
use App\Observers\System\Documents\Traits\EstimateItemable;

class EstimatesServicesItemObserver extends BaseObserver
{
    use EstimateItemable;

    /**
     * Handle the estimate service item "creating" event.
     *
     * @param EstimatesServicesItem $estimatesServicesItem
     */
    public function creating(EstimatesServicesItem $estimatesServicesItem)
    {
        $this->store($estimatesServicesItem);
    }

    /**
     * Handle the estimate service item "updating" event.
     *
     * @param EstimatesServicesItem $estimatesServicesItem
     */
    public function updating(EstimatesServicesItem $estimatesServicesItem)
    {
        $this->update($estimatesServicesItem);
        $this->setAggregations($estimatesServicesItem);
    }

    /**
     * Handle the estimate service item "deleting" event.
     *
     * @param EstimatesServicesItem $estimatesServicesItem
     */
    public function deleting(EstimatesServicesItem $estimatesServicesItem)
    {
        $this->destroy($estimatesServicesItem);
    }
}
