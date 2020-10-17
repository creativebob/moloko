<?php

namespace App\Models\System\Stocks;

use App\Goods;

class GoodsStock extends CmvStock
{
    public function cmv()
    {
        return $this->belongsTo(Goods::class);
    }
}
