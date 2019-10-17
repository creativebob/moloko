<?php

namespace App\Observers;

use App\EstimatesGoodsItem;

use App\Observers\Traits\Commonable;

class EstimatesGoodsItemObserver
{

    use Commonable;

    public function creating(EstimatesGoodsItem $estimates_goods_item)
    {
        $this->store($estimates_goods_item);
    }

    public function updating(EstimatesGoodsItem $estimates_goods_item)
    {
        $this->update($estimates_goods_item);
    }

    public function deleting(EstimatesGoodsItem $estimates_goods_item)
    {
        $this->destroy($estimates_goods_item);
    }
}
