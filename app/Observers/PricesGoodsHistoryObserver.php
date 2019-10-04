<?php

namespace App\Observers;

use App\PricesGoodsHistory;

use App\Observers\Traits\Commonable;

class PricesGoodsHistoryObserver
{

    use Commonable;

    public function creating(PricesGoodsHistory $prices_goods_history)
    {
        $this->store($prices_goods_history);
        $this->setBeginDate($prices_goods_history);
    }

    public function updating(PricesGoodsHistory $prices_goods_history)
    {
        $this->update($prices_goods_history);
    }

    public function deleting(PricesGoodsHistory $prices_goods_history)
    {
        $this->destroy($prices_goods_history);
    }

    protected function setBeginDate($prices_goods_history)
    {
        $prices_goods_history->begin_date = now();
    }

}
