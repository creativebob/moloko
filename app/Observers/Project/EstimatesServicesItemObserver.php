<?php

namespace App\Observers\Project;

use App\Models\Project\EstimatesServicesItem;

use App\Observers\System\Documents\Traits\EstimateItemable;

class EstimatesServicesItemObserver
{
    use EstimateItemable;

    /**
     * Handle the estimatesGoodsItem "creating" event.
     *
     * @param EstimatesServicesItem $estimatesServicesItem
     */
    public function creating(EstimatesServicesItem $estimatesServicesItem)
    {
        $this->setAggregations($estimatesServicesItem);
    }

}
