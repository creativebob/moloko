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
        if ($discount->isDirty('mode') || $discount->isDirty('is_block')) {
//            dd($discount->isDirty('is_block'));
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
