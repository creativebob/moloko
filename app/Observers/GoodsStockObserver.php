<?php

namespace App\Observers;

use App\GoodsStock;
use App\Observers\Traits\Commonable;

class GoodsStockObserver
{

    use Commonable;

    public function creating(GoodsStock $goods_stock)
    {
        $this->store($goods_stock);
    }

    public function updating(GoodsStock $goods_stock)
    {
        $this->update($goods_stock);
    }

    public function deleting(GoodsStock $goods_stock)
    {
        $this->destroy($goods_stock);
    }

}
