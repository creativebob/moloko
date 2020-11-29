<?php

namespace App\Observers\System\Parser;

use App\Models\System\Parser\EstimatesGoodsItem;
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
