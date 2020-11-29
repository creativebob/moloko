<?php

namespace App\Observers\Project;

use App\Models\Project\EstimatesGoodsItem;

use App\Observers\System\Documents\Traits\EstimateItemable;

class EstimatesGoodsItemObserver
{
    use EstimateItemable;

    /**
     * Handle the estimatesGoodsItem "creating" event.
     *
     * @param EstimatesGoodsItem $estimatesGoodsItem
     */
    public function creating(EstimatesGoodsItem $estimatesGoodsItem)
    {
        $this->setAggregations($estimatesGoodsItem);
    }

}
