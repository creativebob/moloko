<?php

namespace App\Observers\System;

use App\Discount;
use App\Observers\System\Traits\Discountable;
use App\Observers\System\Traits\Commonable;
use App\PricesGoods;

class PricesGoodsObserver
{

    use Commonable;
    use Discountable;

    public function creating(PricesGoods $priceGoods)
    {
        $this->store($priceGoods);
        $priceGoods->display = true;

        // TODO - 19.11.19 - Пока по дефолту рубль
        $priceGoods->currency_id = 1;

        $this->setDiscountsPriceGoods($priceGoods);
    }

    public function created(PricesGoods $priceGoods)
    {
        $priceGoods->history()->create([
            'price' => $priceGoods->price,
            'currency_id' => $priceGoods->currency_id,
        ]);
    }

    public function updating(PricesGoods $priceGoods)
    {
        $this->update($priceGoods);
        $this->setDiscountsPriceGoods($priceGoods);
    }

    public function deleting(PricesGoods $priceGoods)
    {
        $this->destroy($priceGoods);
    }
}
