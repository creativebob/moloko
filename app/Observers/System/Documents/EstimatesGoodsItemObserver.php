<?php

namespace App\Observers\System\Documents;

use App\Models\System\Documents\EstimatesGoodsItem;
use App\Observers\System\BaseObserver;
use App\Observers\System\Documents\Traits\EstimateItemable;

class EstimatesGoodsItemObserver extends BaseObserver
{

    use EstimateItemable;

    /**
     * Handle the estimate goods item "creating" event.
     *
     * @param EstimatesGoodsItem $estimatesGoodsItem
     */
    public function creating(EstimatesGoodsItem $estimatesGoodsItem)
    {
        $this->store($estimatesGoodsItem);
    }

    /**
     * Handle the estimate goods item "updating" event.
     *
     * @param EstimatesGoodsItem $estimatesGoodsItem
     */
    public function updating(EstimatesGoodsItem $estimatesGoodsItem)
    {
        $this->update($estimatesGoodsItem);
        $this->setAggregations($estimatesGoodsItem);
    }

    /**
     * Handle the estimate goods item "deleting" event.
     *
     * @param EstimatesGoodsItem $estimatesGoodsItem
     */
    public function deleting(EstimatesGoodsItem $estimatesGoodsItem)
    {
        $this->destroy($estimatesGoodsItem);
    }
}
