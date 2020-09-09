<?php

namespace App\Observers\System;

use App\Discount;
use App\Observers\System\Traits\Commonable;
use App\Observers\System\Traits\Discountable;
use App\PricesGoods;

class DiscountObserver
{

    use Commonable;
    use Discountable;

    public function creating(Discount $discount)
    {
        $this->store($discount);

        if ($discount->begined_at <= now() && ($discount->ended_at > now() || is_null($discount->ended_at))) {
            $discount->is_actual = true;
        }
    }

    public function created(Discount $discount)
    {
        if ($discount->entity->alias == 'estimates') {
            $this->recalculatingPrices($discount);
        }
    }

    public function updating(Discount $discount)
    {

        $this->update($discount);
    }

    public function deleting(Discount $discount)
    {
        $this->destroy($discount);
    }

    public function recalculatingPrices(Discount $discount)
    {
        $pricesGoods = PricesGoods::where([
            'company_id' => auth()->user()->company_id,
            'archive' => false
        ])
            ->get();

        foreach ($pricesGoods as $priceGoods) {
            $priceGoods->update([
                'estimate_discount_id' => $discount->id
            ]);
        }
    }

    // TODO - 08.09.20 - Т.к. решили в скидке не менять значения, то этот блок не актуален

//    public function updated(Discount $discount)
//    {
//
//        if (! $discount->archive) {
//            $this->recalculating($discount);
//        }
//    }

    /**
     * Пересчитываем скидки при изменении самой скидки к подключенным к ней сущностям, в зависимости от типа
     *
     * @param $discount
     */
    public function recalculating(Discount $discount)
    {

        if ($discount->isDirty('mode') || $discount->isDirty('is_block')) {
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
