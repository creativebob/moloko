<?php

namespace App\Observers\System;

use App\Discount;
use App\Observers\System\Traits\Commonable;
use App\Observers\System\Traits\Discountable;

class DiscountObserver
{

    use Commonable;
    use Discountable;

    public function creating(Discount $discount)
    {
        $this->store($discount);
    }

    public function updating(Discount $discount)
    {

        $this->update($discount);
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

    public function recalculating($discount)
    {

        if ($discount->isDirty('mode') || $discount->isDirty('is_block') || $discount->isDirty('begined_at') || $discount->isDirty('ended_at')) {
            switch($discount->entity->alias) {
                case ('prices_goods'):
                    $discount->load([
                        'prices_goods_actual'
                    ]);
                    foreach ($discount->prices_goods_actual as $priceGoods) {
                        $priceGoods = $this->setDiscountsPriceGoods($priceGoods);
                        $priceGoods->save();
                    }
                    break;

                case ('catalogs_goods_items'):
                    $discount->load([
                        'catalogs_goods_items_prices_goods_actual'
                    ]);
                    foreach ($discount->catalogs_goods_items_prices_goods_actual as $priceGoods) {
                        $priceGoods = $this->setDiscountsPriceGoods($priceGoods);
                        $priceGoods->save();
                    }
                    break;

                case('estimates'):
                    $discount->load([
                        'estimates_prices_goods_actual'
                    ]);

                    foreach($discount->estimates_prices_goods_actual as $priceGoods) {
                        $priceGoods->update([
                            'estimate_discount_id' => null
                        ]);
                    }
                    break;
            }
        }
    }
}
