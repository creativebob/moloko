<?php

namespace App\Observers\System\Stocks;

use App\Models\System\Stocks\GoodsStock;

class GoodsStockObserver extends CmvStockObserver
{

    public function creating(GoodsStock $goodsStock)
    {
        $this->store($goodsStock);
    }

    public function updating(GoodsStock $goodsStock)
    {
        $this->update($goodsStock);
        $this->setFree($goodsStock);
    }

    public function deleting(GoodsStock $goodsStock)
    {
        $this->destroy($goodsStock);
    }
}
