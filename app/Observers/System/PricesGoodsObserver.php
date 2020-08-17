<?php

namespace App\Observers\System;

use App\PricesGoods;

use App\Observers\System\Traits\Commonable;

class PricesGoodsObserver
{

    use Commonable;

    public function creating(PricesGoods $priceGoods)
    {
        $this->store($priceGoods);
        $priceGoods->display = true;

        // TODO - 19.11.19 - Пока по дефолту рубль
        $priceGoods->currency_id = 1;

        $this->setTotal($priceGoods);
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
        $this->setTotal($priceGoods);
    }

    public function deleting(PricesGoods $priceGoods)
    {
        $this->destroy($priceGoods);
    }

    public function setTotal($priceGoods)
    {
        $priceGoods->total = $priceGoods->price - $priceGoods->discount_currency;
    }

}
