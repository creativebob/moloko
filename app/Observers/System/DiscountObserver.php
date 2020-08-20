<?php

namespace App\Observers\System;

use App\Discount;
use App\Observers\System\Traits\Commonable;
use App\Observers\System\Traits\Discountable;
use App\Observers\System\Traits\Timestampable;

class DiscountObserver
{

    use Commonable;
    use Timestampable;
    use Discountable;

    public function creating(Discount $discount)
    {
        $this->store($discount);
        $this->setBeginedAt($discount);
        $this->setEndedAt($discount);
    }

    public function updating(Discount $discount)
    {
        $this->update($discount);

        if (! $discount->archive) {
            $this->setBeginedAt($discount);
            $this->setEndedAt($discount);
        }
    }
    
    public function updated(Discount $discount)
    {
        
        if (! $discount->archive) {
            $this->recalculating($discount);
        }
    }

    public function deleting(Discount $discount)
    {
        $this->destroy($discount);
    }

    public function setBeginedAt($discount)
    {
        $beginedAt = $this->getTimestamp('begin', true);
        $discount->begined_at = $beginedAt;
    }

    public function setEndedAt($discount)
    {
        $endedAt = $this->getTimestamp('end');
        $discount->ended_at = $endedAt;
    }
    
    public function recalculating($discount)
    {
        if ($discount->isDirty('mode')) {
            switch($discount->entity->alias) {
                case ('prices_goods'):
                    $discount->load('prices_goods_actual');
                    
                    foreach ($discount->prices_goods_actual as $priceGoods) {
                        $priceGoods = $this->setDiscountsPriceGoods($priceGoods);
                        $priceGoods->save();
                    }
                    
                    break;
        
                case ('catalogs_goods_items'):
                    $discount->load([
                        'catalogs_goods_items' => function ($q) use ($discount) {
                            $q->whereHas('discounts_actual', function ($q) use ($discount) {
                                $q->where('id', $discount->id);
                            })
                            ->with('prices_goods_actual');
                        }
                    ]);
                    foreach ($discount->catalogs_goods_items as $catalogsGoodsItem) {
                        foreach ($catalogsGoodsItem->prices_goods_actual as $priceGoods) {
                            $priceGoods = $this->setDiscountsPriceGoods($priceGoods);
                            $priceGoods->save();
                        }
//                        $this->updateDiscountCatalogsGoodsItem($catalogsGoodsItem, $discount);
                    }
                    break;
            }
        }
    }
}
