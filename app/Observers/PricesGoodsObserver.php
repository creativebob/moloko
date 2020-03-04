<?php

namespace App\Observers;

use App\PricesGoods;

use App\Observers\Traits\Commonable;

class PricesGoodsObserver
{

    use Commonable;

    public function creating(PricesGoods $prices_goods)
    {
        $this->store($prices_goods);
        $prices_goods->display = true;

        // TODO - 19.11.19 - Пока по дефолту рубль
        $prices_goods->currency_id = 1;
    }

    public function created(PricesGoods $prices_goods)
    {
        $prices_goods->history()->create([
            'price' => $prices_goods->price,
            'currency_id' => $prices_goods->currency_id,
        ]);
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
