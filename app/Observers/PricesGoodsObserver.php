<?php

namespace App\Observers;

use App\PricesGoods;

use App\Observers\Traits\CommonTrait;

class PricesGoodsObserver
{

    use CommonTrait;

    public function creating(PricesGoods $prices_goods)
    {
        $this->store($prices_goods);
    }

    public function updating(PricesGoods $prices_goods)
    {
        $this->update($prices_goods);
    }

    public function deleting(PricesGoods $prices_goods)
    {
        $this->destroy($prices_goods);
    }

}
