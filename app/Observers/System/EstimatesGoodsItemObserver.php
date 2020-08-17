<?php

namespace App\Observers\System;

use App\EstimatesGoodsItem;

use App\Observers\System\Traits\Commonable;

class EstimatesGoodsItemObserver
{

    use Commonable;

    public function creating(EstimatesGoodsItem $estimatesGoodsItem)
    {
        $this->store($estimatesGoodsItem);

        $this->setAggregations($estimatesGoodsItem);
    }

    public function updating(EstimatesGoodsItem $estimatesGoodsItem)
    {
        $this->update($estimatesGoodsItem);

        $this->setAggregations($estimatesGoodsItem);

    }

    public function deleting(EstimatesGoodsItem $estimatesGoodsItem)
    {
        $this->destroy($estimatesGoodsItem);
    }

    public function setAggregations($estimatesGoodsItem)
    {
        $estimatesGoodsItem->load('product.article');
        $saleMode = $estimatesGoodsItem->sale_mode;

        switch ($saleMode) {
            case (1):
                $estimatesGoodsItem->total_points = 0;
                $estimatesGoodsItem->total_bonuses = 0;

                $estimatesGoodsItem->cost = $estimatesGoodsItem->count * $estimatesGoodsItem->product->article->cost_default;
                $estimatesGoodsItem->amount = $estimatesGoodsItem->count * $estimatesGoodsItem->price;

                $estimatesGoodsItem->total = $estimatesGoodsItem->amount - ($estimatesGoodsItem->discount_currency * $estimatesGoodsItem->count);

                $estimatesGoodsItem->margin_currency = $estimatesGoodsItem->total - $estimatesGoodsItem->cost;
                if ($estimatesGoodsItem->total > 0) {
                    $estimatesGoodsItem->margin_percent = ($estimatesGoodsItem->margin_currency / $estimatesGoodsItem->total * 100);
                } else {
                    $estimatesGoodsItem->margin_percent = ($estimatesGoodsItem->margin_currency * 100);
                }

                break;

            case (2):
                $estimatesGoodsItem->amount = 0;
                $estimatesGoodsItem->discount_currency = 0;
                $estimatesGoodsItem->discount_percent = 0;
                $estimatesGoodsItem->margin_currency = 0;
                $estimatesGoodsItem->margin_percent = 0;
                $estimatesGoodsItem->total = 0;
                $estimatesGoodsItem->total_bonuses = 0;
                $estimatesGoodsItem->total_points = $estimatesGoodsItem->count * $estimatesGoodsItem->points;
                break;
        }
    }

}
